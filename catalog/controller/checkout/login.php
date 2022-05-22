<?php
class ControllerCheckoutLogin extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');
		$this->load->model('account/customer');

		$data['checkout_guest'] = ($this->config->get('config_checkout_guest') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());

		if (isset($this->session->data['account'])) 
		{
			$data['account'] = $this->session->data['account'];
		} 
		else 
		{
			$data['account'] = 'register';
		}

			$data['forgotten'] = $this->url->link('account/forgotten', '', true);
		$data['action'] = $this->url->link('checkout/login', '', true);
		$data['register'] = $this->url->link('common/otp_validation', '', true);
	//	$data['forgotten'] = $this->url->link('account/forgotten', '', true);

		$this->response->setOutput($this->load->view('checkout/login', $data));
	}

	public function save() {
		$this->load->language('checkout/checkout');
		$this->load->model('account/customer');

		$json = array();

		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}


		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		if (!$json) {
			$this->load->model('account/customer');

			// Check how many login attempts have been made.
			$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

			if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
				$json['error']['warning'] = $this->language->get('error_attempts');
			}

			// Check if customer has been approved.
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

			if ($customer_info && !$customer_info['status']) {
				$json['error']['warning'] = $this->language->get('error_approved');
			}

			if (!isset($json['error'])) {
				if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
					$json['error']['warning'] = $this->language->get('error_login');

					$this->model_account_customer->addLoginAttempt($this->request->post['email']);
				} else {
					$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
				}
			}
		}

		if (!$json) {
			// Unset guest
			unset($this->session->data['guest']);

			// Default Shipping Address
			$this->load->model('account/address');
			$this->load->model('account/customer');

			if ($this->config->get('config_tax_customer') == 'payment') {
				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}

			if ($this->config->get('config_tax_customer') == 'shipping') {
				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}

			// Wishlist
			if (isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist'])) {
				$this->load->model('account/wishlist');

				foreach ($this->session->data['wishlist'] as $key => $product_id) {
					$this->model_account_wishlist->addWishlist($product_id);

					unset($this->session->data['wishlist'][$key]);
				}
			}

			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	
	public function otpcompare()
	{
		$this->load->language('checkout/checkout');
		$this->load->model('account/customer');

		$json = array();

        if(isset($this->request->post['enteredotp']))
		{
           $otpcompare=(int)$this->request->post['enteredotp'];
		}

		if(isset($this->request->post['phoneno']))
		{
             $phoneno=(int)$this->request->post['phoneno'];
		}

		 if($_COOKIE['sevaMartLoginOtp'])
		 
		{

				if($_COOKIE['sevaMartLoginOtp'] != $this->request->post['enteredotp'])
				{

				$json['incorrectOTP']="Invalid OTP";

				}

						
			else
			{

				if($_COOKIE['sevaMartLoginOtp'] == $this->request->post['enteredotp'])
				
				{

					$customer_info = $this->model_account_customer->getCustomerByTelephone($phoneno );

					if ($customer_info && $this->customer->login($customer_info['email'], '', true))

						{
							$json['redirect'] = $this->url->link('checkout/checkout', '', true);
							

						}

				}
					
			}

		  
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	

	public function phoneNumberValidation()
	{
		$json = array();

        if (isset($this->request->post['phoneNo']))
		{
			$phoneNo = (int)$this->request->post['phoneNo'];


			$query  = $this->db->query("SELECT * FROM `oc_customer` WHERE telephone= '".$phoneNo."' ");

			$count=count( $query->rows);
	
			if($count > 0){

				$json['response'] = "vaild phone number";
				
				$otp = mt_rand(100000, 999999);
				

            $apiKey = urlencode('NmIzNTZiNDM2ZTMyNjc0YTU1NmI3NzY5NTM0YzMzMzQ=');
	
            // Message details
			$numbers = 	$phoneNo;

			$sender = urlencode('600010');

			$message = rawurlencode('Hi there, thank you for sending your first test message from Textlocal. Get 20% off today with our code: '.$otp.".");		 

			// Prepare data for POST request
			$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
		 
			//Send the POST request with cURL
			// $ch = curl_init('https://api.textlocal.in/send/');
			// curl_setopt($ch, CURLOPT_POST, true);
			// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// $response = curl_exec($ch);
			
			//Process your response here
			//echo $response;


                //if($response['status'] == "success"){

				setcookie("sevaMartLoginOtp", $otp);
				$json['responseOtp'] = $otp;
				
				//}
				
					//$json['status'] = $response['status'];
				
				//$this->session->data['sevaMartLoginOtp'] = $otp;
			
			}
			else{
				$json['response'] = "Invaild phone number";
			}
			  
	
           } 
		   else 
		   {
			  $json['response'] = "Enter phone Number";
	       }

            //  $apiKey = urlencode('NmIzNTZiNDM2ZTMyNjc0YTU1NmI3NzY5NTM0YzMzMzQ=');
	
            // echo $apiKey;

            // // Message details
			// $numbers = 	$enterdMobileNO;

	    
			// $sender = urlencode('600010');

			// // $otp = mt_rand(10000, 99999);

			// $message = "Hi there, thank you for sending your first test message from Textlocal. See how you can send effective SMS campaigns here: https://tx.gl/r/2nGVj/";
		 

			// // var_dump($numbers);
		 
			// // Prepare data for POST request
			// $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
		 
			// Send the POST request with cURL
		// 	$ch = curl_init('https://api.textlocal.in/send/');
		// 	curl_setopt($ch, CURLOPT_POST, true);
		// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// //	$response = curl_exec($ch);
		// 	curl_close($ch);
			
		// 	//Process your response here
		// 	echo $response;

        /*   
		
		if (isset($this->request->post['otp']) )
		{
		  // Unset guest
		//  var_dump($_COOKIE['sevaMartLoginOtp']);
		if($_COOKIE['sevaMartLoginOtp'] != $this->request->post['enteredOtp'])
		{

			//$this->error['warning'] = "Invalid OTP";
			//$data['mobile'] = $this->request->post['mobile'];
		}

		else
		{

		$customer_info = $this->model_account_customer->getCustomerByTelephone($this->request->post['mobile']);

	if(	$customer_info)
	  {
				if ($this->customer->isLogged()) 
				{
					$json['redirect'] = $this->url->link('checkout/checkout', '', true);
				}

	}

			}
		}

		*/

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

           	}



   public function registerPhoneNumberValidation()
	{
		$json = array();

        if (isset($this->request->post['phoneNo']))
		{
			$phoneNo = (int)$this->request->post['phoneNo'];


			//$query  = $this->db->query("SELECT * FROM `oc_customer` WHERE telephone= '".$phoneNo."' ");

			//$count=count( $query->rows);
	
			if(true){

				$json['response'] = "vaild phone number";
				
				$otp = mt_rand(100000, 999999);
				

            $apiKey = urlencode('NmIzNTZiNDM2ZTMyNjc0YTU1NmI3NzY5NTM0YzMzMzQ=');
	
            // Message details
			$numbers = 	$phoneNo;

			$sender = urlencode('600010');

			$message = rawurlencode('Hi there, thank you for sending your first test message from Textlocal. Get 20% off today with our code: '.$otp.".");		 

			// Prepare data for POST request
			$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
		 
			//Send the POST request with cURL
			// $ch = curl_init('https://api.textlocal.in/send/');
			// curl_setopt($ch, CURLOPT_POST, true);
			// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// $response = curl_exec($ch);
			
			//Process your response here
			//echo $response;


                //if($response['status'] == "success"){

				setcookie("sevaMartLoginOtp", $otp);
				$json['responseOtp'] = $otp;
				
				//}
				
					//$json['status'] = $response['status'];
				
				//$this->session->data['sevaMartLoginOtp'] = $otp;
			
			}
			
			  
	
           } 
		   else 
		   {
			  $json['response'] = "Enter phone Number";
	       }


      
            //  $apiKey = urlencode('NmIzNTZiNDM2ZTMyNjc0YTU1NmI3NzY5NTM0YzMzMzQ=');
	
            // echo $apiKey;

            // // Message details
			// $numbers = 	$enterdMobileNO;

	    
			// $sender = urlencode('600010');

			// // $otp = mt_rand(10000, 99999);

			// $message = "Hi there, thank you for sending your first test message from Textlocal. See how you can send effective SMS campaigns here: https://tx.gl/r/2nGVj/";
		 

			// // var_dump($numbers);
		 
			// // Prepare data for POST request
			// $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
		 
			// Send the POST request with cURL
		// 	$ch = curl_init('https://api.textlocal.in/send/');
		// 	curl_setopt($ch, CURLOPT_POST, true);
		// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// //	$response = curl_exec($ch);
		// 	curl_close($ch);
			
		// 	//Process your response here
		// 	echo $response;


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

           	}

	protected function validate() {
		// Check how many login attempts have been made.
		$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

		

		if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) 
		{
			$this->error['warning'] = $this->language->get('error_attempts');
		}

		// Check if customer has been approved.
		$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

		if ($customer_info && !$customer_info['status']) {
			$this->error['warning'] = $this->language->get('error_approved');
		}

		if (!$this->error) {
			if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {

             

	           $this->error['warning'] = $this->language->get('error_login');

				$this->model_account_customer->addLoginAttempt($this->request->post['email']);
			} 
			
			else
			 {
              

				$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
			}
		}

		return !$this->error;
	}


	protected function validatePhoneNumber() {
		// Check how many login attempts have been made.
		// $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

		

		// if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) 
		// {
		// 	$this->error['warning'] = $this->language->get('error_attempts');
		// }

		// Check if customer has been approved.
		$customer_info = $this->model_account_customer->getCustomerByTelephone($this->request->post['mobile']);

		if ($customer_info && !$customer_info['status']) {
			$this->error['warning'] = $this->language->get('error_approved');
		}

		if (!$this->error) {
			if (!$this->customer->loginByPhoneNumber($this->request->post['mobile'])) {

               echo "  add login vaaalidate";

	            $this->error['warning'] = $this->language->get('error_login');

				//$this->model_account_customer->addLoginAttempt($this->request->post['email']);
			} 
			
			else
			 {
               echo "   delete  login vaaalidate";


				//$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
			}
		}

		return !$this->error;
	}


}

//ffff
