<?php

require_once __DIR__.'/../../../../system/library/razorpay-sdk/Razorpay.php';
use Razorpay\Api\Api;
use Razorpay\Api\Errors;

class ControllerExtensionPaymentRazorpay extends Controller
{
    /**
     * Event constants
     */
    const PAYMENT_AUTHORIZED    = 'payment.authorized';
    const PAYMENT_FAILED        = 'payment.failed';
    const ORDER_PAID            = 'order.paid';

    // Set RZP plugin version
    private $version = '4.0.0';
  
    public function index()
    {
        $data['button_confirm'] = $this->language->get('button_confirm');
        //$data['order_id'] = "order_GZRukQ35hdvrqz";
        //var_dump ($data['order_id']);
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
       
        $this->load->model('storecontract/storecontract');
        $store_contract_info = $this->model_storecontract_storecontract->getStoreSubscription( $this->config->get('config_store_id')); 

        // Orders API with payment autocapture
        try 
        { 
            $api = $this->getApiIntance();
            
            $order_data = $this->get_order_creation_data($this->session->data['order_id']);   
            //var_dump($order_data );
            $razorpay_order = $api->order->create($order_data);
        }

        catch(\Razorpay\Api\Errors\Error $e)
        {
            $this->log->write($e->getMessage());
            $this->session->data['error'] = $e->getMessage();//exit;
            echo "<div class='alert alert-danger alert-dismissible'> Something went wrong. Unable to create Razorpay Order Id.</div>";
            exit;
        }
        $this->session->data['razorpay_order_id'] = $razorpay_order['id'];

        $data['key_id'] = $store_contract_info['razorpay_key_id'];
        $data['currency_code'] = $order_info['currency_code'];
        $data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
        $data['merchant_order_id'] = $this->session->data['order_id'];
        $data['card_holder_name'] = $order_info['payment_firstname'].' '.$order_info['payment_lastname'];
        $data['email'] = $order_info['email'];
        $data['phone'] = $order_info['telephone'];
        $data['name'] = $this->config->get('config_name');
        $data['lang'] = $this->session->data['language'];
        $data['return_url'] = $this->url->link('extension/payment/razorpay/callback', '', 'true');
        $data['razorpay_order_id'] = $razorpay_order['id'];
        $data['version'] = $this->version;
        $data['oc_version'] = VERSION;

        //varify if 'hosted' checkout required and set related data        
        $this->getMerchantPreferences($data);

        $data['api_url']    = $api->getBaseUrl();
        $data['cancel_url'] =  $this->url->link('checkout/checkout', '', 'true');

        if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/extension/payment/razorpay')) 
        {
            return $this->load->view($this->config->get('config_template').'/template/extension/payment/razorpay', $data);
        } 
        else 
        {
            return $this->load->view('extension/payment/razorpay', $data);
        }
    }

    private function get_order_creation_data($order_id)
    { 
         $this->load->model('storecontract/storecontract');
       
         $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

       //  var_dump($order);
      //   echo "<br>";
    //modified on 10/02/2021 -----------

         $store_id = $order['store_id'];
      //  var_dump(  $store_id);

        // echo "<br>";
         $storecontractquery = $this->model_storecontract_storecontract->getStoreSubscription($store_id);

      //  var_dump( $storecontractquery);

        $commission=  $storecontractquery['commission_percentage_card'];
       
      //  echo "<br>";

           //echo "the cpd is $commission";
          //  echo "<br>";
         //   $account_id=  $storecontractquery['account_id'];
                
        //     echo "the aID i $account_id";
        //    echo "<br>";


         $setting_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape("config") . "'");
    
    
    
        //  $commission_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape("config_payment") . "'");
        
        // foreach ($query->rows as $result) {

		// 	if (!$result['serialized']) {
		// 		$setting_data[$result['key']] = $result['value'];
		// 	} else {
		// 		$setting_data[$result['key']] = json_decode($result['value'], true);
		// 	}

		// }

        //  $account_id = $setting_data['config_account'];
        //  $commission = $setting_data['config_payment'];
        //  var_dump($account_id);
        //  var_dump($commission);

        $commmission_percentage = $commission / 100;
      // echo     $commmission_percentage ;
        $totalAmount = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false) * 100;
        $commissionAmount = $commmission_percentage * $totalAmount;
    //   echo "<br>";
     // var_dump(  $commissionAmount);
        $amountToBeTransvered = $totalAmount - $commissionAmount;
       // var_dump($acc_query);

        // $data = [
        //     'receipt' => $order_id,
        //     'amount' => $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false) * 100,
        //     'currency' => $order['currency_code'],
        //     'payment_capture' => ($this->config->get('payment_razorpay_payment_action') === 'authorize') ? 0 : 1,
        //     'transfers' => [
        //                         [
        //                         "account" => $account_id ,
        //                         "amount" => (int)$amountToBeTransvered,
        //                         "currency" => $order['currency_code'],
        //                         "notes" => [
        //                             "branch" => " ",
        //                             "name" => " "
        //                         ],
        //                         "linked_account_notes" => [
        //                             "branch"
        //                         ],
        //                         "on_hold" => 0
        //                         ]
        //                     ]
        // ];

        $data = [
            'receipt' => $order_id,
            'amount' => $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false) * 100,
            'currency' => $order['currency_code'],
            'payment_capture' => ($this->config->get('payment_razorpay_payment_action') === 'authorize') ? 0 : 1
        ];


        return $data;
    }

// ------------------------------------
    public function callback()
    {
        $this->load->model('checkout/order');

        if (isset($this->request->request['razorpay_payment_id']) === true) 
        {    
            $razorpay_payment_id = $this->request->request['razorpay_payment_id'];
            $merchant_order_id = $this->session->data['order_id'];
            $razorpay_order_id = $this->session->data['razorpay_order_id']; 
            $razorpay_signature = $this->request->request['razorpay_signature'];

            $order_info = $this->model_checkout_order->getOrder($merchant_order_id);
            $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
            
            //validate Rzp signature
            $api = $this->getApiIntance();
            try
            {                
                $attributes = array(
                    'razorpay_order_id' => $razorpay_order_id,
                    'razorpay_payment_id' => $razorpay_payment_id,
                    'razorpay_signature' => $razorpay_signature
                );

                $api->utility->verifyPaymentSignature($attributes);
                
                $this->model_checkout_order->addOrderHistory($merchant_order_id, $this->config->get('payment_razorpay_order_status_id'), 'Payment Successful. Razorpay Payment Id:'.$razorpay_payment_id, true, false, $razorpay_payment_id);              
                $this->response->redirect($this->url->link('checkout/success', '', true));
            }
            catch(\Razorpay\Api\Errors\SignatureVerificationError $e)
            {
                $this->model_checkout_order->addOrderHistory($merchant_order_id, 10, $e->getMessage() .' Payment Failed! Check Razorpay dashboard for details of Payment Id:'.$razorpay_payment_id, false, false, $razorpay_payment_id);
                
                $this->session->data['error'] = $e->getMessage() .' Payment Failed! Check Razorpay dashboard for details of Payment Id:'.$razorpay_payment_id;
                $this->response->redirect($this->url->link('checkout/checkout', '', true));
            }
        }  
        else 
        {
            if (isset($_POST['error']) === true)
            {
                $error = $_POST['error'];

                $message = 'An error occured. Description : ' . $error['description'] . '. Code : ' . $error['code'];

                if (isset($error['field']) === true)
                {
                    $message .= 'Field : ' . $error['field'];
                }
            } 
            else 
            {
                $message = 'An error occured. Please contact administrator for assistance';
            }
            $this->session->data['error'] = $message;
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }
    }


    public function webhook()
    {  
        $post = file_get_contents('php://input');
        $data = json_decode($post, true);        

        if (json_last_error() !== 0)
        {
            return;
        }
        $this->load->model('checkout/order');
        $enabled = $this->config->get('payment_razorpay_webhook_status');

        if (($enabled === '1') and
            (empty($data['event']) === false))
        {
            
            if (isset($_SERVER['HTTP_X_RAZORPAY_SIGNATURE']) === true)
            {                
                try
                {
                    $this->validateSignature($post , $_SERVER['HTTP_X_RAZORPAY_SIGNATURE']);       
                }
                catch (\Razorpay\Api\Errors\SignatureVerificationError $e)
                {
                    $this->log->write($e->getMessage());
                    header('Status: 400 Signature Verification failed', true, 400);    
                    exit;
                }

                switch ($data['event'])
                {
                    case self::PAYMENT_AUTHORIZED:
                        return $this->paymentAuthorized($data);

                    case self::PAYMENT_FAILED:
                        return $this->paymentFailed($data);

                    case self::ORDER_PAID:
                        return $this->orderPaid($data);

                    default:
                        return;
                }   
            }   
        }        
    }

    /**
     * Handling order.paid event    
     * @param array $data Webook Data
     */
    protected function orderPaid(array $data)
    {
       // reference_no (opencart_order_id) should be passed in payload
        $merchant_order_id = $data['payload']['payment']['entity']['notes']['opencart_order_id'];
        $razorpay_payment_id = $data['payload']['payment']['entity']['id'];
        if(isset($merchant_order_id) === true)
        {    
            $order_info = $this->model_checkout_order->getOrder($merchant_order_id);

            if($order_info['payment_code'] === 'razorpay' and
                !$order_info['order_status_id'])
            {

                $this->model_checkout_order->addOrderHistory($merchant_order_id, $this->config->get('payment_razorpay_order_status_id'), 'Payment Successful. Razorpay Payment Id:'.$razorpay_payment_id,false,false, $razorpay_payment_id);
            }
        }
        // Graceful exit since payment is now processed.
        $this->response->addHeader('HTTP/1.1 200 OK');
        $this->response->addHeader('Content-Type: application/json');
    }

    /**
     * Handling payment.failed event    
     * @param array $data Webook Data
     */
    protected function paymentFailed(array $data)
    {
        exit;
    }

    /**
     * Handling payment.authorized event    
     * @param array $data Webook Data
     */
    protected function paymentAuthorized(array $data)
    {
        //verify if we need to consume it as late authorized 
        $max_capture_delay = $this->config->get('payment_razorpay_max_capture_delay') * 60;
        $payment_created_time = $data['payload']['payment']['entity']['created_at'];

        $api = $this->getApiIntance();

        if((time() - $payment_created_time) < $max_capture_delay)
        {
            // reference_no (opencart_order_id) should be passed in payload
            $merchant_order_id = $data['payload']['payment']['entity']['notes']['opencart_order_id'];
            $razorpay_payment_id = $data['payload']['payment']['entity']['id'];
            
            //update the order
            if(isset($merchant_order_id) === true)
            {    
                $order_info = $this->model_checkout_order->getOrder($merchant_order_id);
                
                if($order_info['payment_code'] === 'razorpay' and
                    !$order_info['order_status_id'])
                {
                    try
                    { 
                        $capture_amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;

                        //fetch the payment
                        $payment = $api->payment->fetch($razorpay_payment_id);

                        //capture only if payment status is 'authorized'
                        if($payment->status === 'authorized')
                        {
                            $payment->capture(array('amount' => $capture_amount,
                                                    'currency' => $order_info['currency_code']
                                                    ));
                        }

                        //update the order status in store
                        $this->model_checkout_order->addOrderHistory($merchant_order_id, $this->config->get('payment_razorpay_order_status_id'), 'Payment Successful. Razorpay Payment Id:'.$razorpay_payment_id,false, false, $razorpay_payment_id);
                    }
                    catch(\Razorpay\Api\Errors\Error $e)
                    {
                        $this->log->write($e->getMessage());
                        header('Status: 400 Payment Capture failed', true, 400);
                        exit;
                    }
                    
                }
            }
        }
        // Graceful exit since payment is now processed.
        $this->response->addHeader('HTTP/1.1 200 OK');
        $this->response->addHeader('Content-Type: application/json');
        exit;
    }


    /**
     * @param $payloadRawData
     * @param $actualSignature
     */
    public function validateSignature($payloadRawData, $actualSignature)
    {
        $api = $this->getApiIntance();

        $webhookSecret = $this->config->get('payment_razorpay_webhook_secret');

        if (empty($webhookSecret) === false)
        {
            $api->utility
                 ->verifyWebhookSignature($payloadRawData, $actualSignature, $webhookSecret);
        }

    }

    public function getMerchantPreferences(array &$preferences)
    {
        $api = $this->getApiIntance();

        try
        {
            $response = Requests::get($api->getBaseUrl() . 'preferences?key_id=' . $api->getKey());
        }
        catch (Exception $e)
        {   
            $this->log->write($e->getMessage());
            throw new Exception($e->getMessage(), $e->getHttpCode());
        }

        $preferences['is_hosted'] = false;
        
        if($response->status_code === 200)
        {

            $jsonResponse = json_decode($response->body, true);

            $preferences['image'] = $jsonResponse['options']['image'];
            if(empty($jsonResponse['options']['redirect']) === false)
            {
                $preferences['is_hosted'] = $jsonResponse['options']['redirect'];
            }
        }

    }

    protected function getApiIntance()
    {
       
        $this->load->model('storecontract/storecontract');
        $store_contract_info = $this->model_storecontract_storecontract->getStoreSubscription( $this->config->get('config_store_id')); 

      return new Api( $store_contract_info['razorpay_key_id'], $store_contract_info['razorpay_key_secret'] );
    }

}
