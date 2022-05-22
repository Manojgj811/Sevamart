<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');

        $this->load->model('account/customer');

		
        $customerDetails = $this->model_account_customer->getCustomer($this->customer->getId());
		$storeOwnerDetails = $this->model_account_customer->getStoreOwnerTelephonrByStoreID($this->config->get('config_store_id'));
		//var_dump($customerDetails['telephone']);
		//var_dump($storeOwnerDetails['telephone']);
		
        
		if (isset($this->session->data['order_id'])) {

        //order confirmation message to customer and store owner
		$apiKey = urlencode('NmIzNTZiNDM2ZTMyNjc0YTU1NmI3NzY5NTM0YzMzMzQ=');
	
		// Message details
		
		$sender = urlencode('600010');
        
        $msg = mt_rand(100000, 999999);
		if(!empty($customerDetails['telephone'])){

	    $numbers = 	$customerDetails['telephone'];
        
		$message = rawurlencode('Hi there, thank you for sending your first test message from Textlocal. Get 20% off today with our code: '.$msg.".");		 

		// Prepare data for POST request
		$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
	 
		//Send the POST request with cURL
		// $ch = curl_init('https://api.textlocal.in/send/');
		// curl_setopt($ch, CURLOPT_POST, true);
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// $response = curl_exec($ch);
		
		// //Process your response here
		// echo $response;
		// $decoded_response = json_decode($response, true);

		}

		if(!empty($storeOwnerDetails['telephone'])){

			$numbers = 	$storeOwnerDetails['telephone'];
			
			$message = rawurlencode('Hi there, thank you for sending your first test message from Textlocal. Get 20% off today with our code: '.$msg.".");		 
	
			// Prepare data for POST request
			$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
		 
			//Send the POST request with cURL
			// $ch = curl_init('https://api.textlocal.in/send/');
			// curl_setopt($ch, CURLOPT_POST, true);
			// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// $response = curl_exec($ch);
			
			// //Process your response here
			// echo $response;
			// $decoded_response = json_decode($response, true);
	
			}

		}

		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/store')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('common/store');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['homepageheader'] = $this->load->controller('common/homepageheader');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}