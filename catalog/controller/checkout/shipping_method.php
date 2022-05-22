<?php
class ControllerCheckoutShippingMethod extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');

		$this->load->model('setting/store');
	
		$this->load->model('servicearea/servicearea');         
		$this->load->model('account/customer');
		$this->load->model('account/address');
		        

   	if (isset($this->session->data['shipping_address'])) {
			//Shipping Methods

        	$customer_group = $this->model_account_customer->getCustomer($this->customer->getId());
	    $customerid=$customer_group['firstname'];
	//	echo  "the customer name is   $customerid";
      //	

		$totalstores = $this->model_setting_store->getStores();
    
		$customeraddress=$this->model_account_address->getAddresses();
    
	//	echo "<br>";
		
		$idcurrentstore=$this->config->get('config_store_id');

        $pincoderesults =$this->model_servicearea_servicearea->getPin($idcurrentstore);
  
       $chargeshipping='';
		foreach ($pincoderesults as $result)
		{
			$pincodevalue=$result['pincode_no']; 
			$chargeshipping=$result['delivery_charges'];
                
	    //   echo  "the service area pincodes are $pincodevalue";
			//}var_dump($chargeshipping);
		//	echo "<br>";
		
	$pincodedeliveryresults =$this->model_servicearea_servicearea->getPincodedeliverycharge($idcurrentstore);

	  	foreach($customeraddress as $resultaddress)
		{
			$addresspostcode=$resultaddress['postcode'];
		}
	
	 	//if (in_array($addresspostcode,$pincodevalue))
		  if ($addresspostcode==$pincodevalue)
		    { 
				//echo "ssexceuted";
			  $output=$chargeshipping;
		    }
		
	     }
		//  var_dump($pincodevalue);
		//  var_dump($addresspostcode);	
	//	var_dump($pincodematchddelivery);
		$data['amounts'][] = array(
			'name' =>  $output
			
		);
	

	$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('shipping');

			foreach ($results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);

					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

					if ($quote) {
						$method_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$this->session->data['shipping_methods'] = $method_data;
		}

		 if (empty($this->session->data['shipping_methods'])) {
			$data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['shipping_methods'])) {
			$data['shipping_methods'] = $this->session->data['shipping_methods'];
		} else {
			$data['shipping_methods'] = array();
		}

		if (isset($this->session->data['shipping_method']['code'])) {
			$data['code'] = $this->session->data['shipping_method']['code'];
		} else {
			$data['code'] = '';
		}

		if (isset($this->session->data['comment'])) {
			$data['comment'] = $this->session->data['comment'];
		} else {
			$data['comment'] = '';
		}
		/////////////////////to display delivery charge


	//	echo "the delivery charge is  $output";
	//	echo   "the current storeid is $idcurrentstore";
	
$this->response->setOutput($this->load->view('checkout/shipping_method', $data));
	// 
		}

	public function save() {
		$this->load->language('checkout/checkout');

		$json = array();

		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate if shipping address has been set.
		if (!isset($this->session->data['shipping_address'])) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}

		if (!isset($this->request->post['shipping_method'])) {
			$json['error']['warning'] = $this->language->get('error_shipping');
		} else {
			$shipping = explode('.', $this->request->post['shipping_method']);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			}
		}

		if (!$json) {
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

			$this->session->data['comment'] = strip_tags($this->request->post['comment']);
		}

		//var_dump($this->session->data['shipping_method']);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}