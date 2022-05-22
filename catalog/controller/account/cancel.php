<?php
class ControllerAccountCancel extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/cancel', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}


		if(isset($this->request->get['error'])){
			$data['error_warning'] = $this->request->get['error'];
		}else{
			$data['error_warning'] = "";
		}

		$this->load->language('account/cancel');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/store')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['page2'])) {
			$url .= '&page=' . $this->request->get['page2'];
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_return'),
			'href' => $this->url->link('account/cancel', $url, true)
		);

		$this->load->model('account/return');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['page2'])) {
			$page2 = $this->request->get['page2'];
		} else {
			$page2 = 1;
		}

		$this->load->model('account/order');
		$this->load->model('account/cancel');
		$this->load->model('setting/setting'); 
		$this->load->model('tool/image');

		//$order_total = $this->model_account_order->getTotalDeliveredOrders();

		$results = $this->model_account_order->getOrders();
      
       foreach ($results as $result) {

		
		  if($result['status'] == "Order Processing"){

			$products = $this->model_account_order->getOrderProducts($result['order_id']);

		    foreach ($products as $product){

				$product_status_id =  $this->model_account_return->getProductStatus($result['order_id'], $product['product_id']);
                
				//refer oc_return_status_id
				if($product_status_id == 3){

					$requestedDate = $this->model_account_cancel->getRequestedDate($result['order_id'], $product['product_id']);
				}
				else{

					$requestedDate['request_type'] = '';
					$requestedDate['date_added'] = '';
				}
			    $productImage = $this->model_account_order->getProductImage($product['product_id']);
				$data['Images'] = $this->model_tool_image->resize($productImage['image'], 120, 75);
				 
							$data['productsList'][] = array(
						    'store_id' =>  $result['store_id'],		
						    'order_id'   => $result['order_id'],			
							'product_id' => $product['product_id'],
							'status' => $result['status'],
							'product_status' => $product_status_id,
							'image' => $data['Images'],
							'name' => $productImage['name'],
							'quantity' => $product['quantity'],
							'total' => (int)$product['total'] + (int)$product['tax'],
							'requested_action' => $requestedDate['request_type'],
							'date_added' => $requestedDate['date_added'],
							
						);
				
				
	          }
		   }	

        }



		//var_dump($data['productsList']);

		$newArray = array_filter($results, function ($var) {
			return ($var['status'] == 'Order Processing');
		});

		//var_dump($newArray);

		$output = array_intersect_key(
			$newArray, 
			array_unique(array_map(function($item) {
				return $item['store_id'];
			}, $newArray))
		);


		foreach ($output as $result) {

			$logoresult=$this->model_setting_setting->getSetting('config', $result['store_id']);
            if (is_file(DIR_IMAGE . $logoresult['config_logo'])) 
                {
                    $data['storelogo'] = $this->model_tool_image->resize($logoresult['config_logo'], 120, 75);
                } 

			$data['stores'][] = array(
				
				'store_name' => $result['store_name'],
				'status'     => $result['status'],
				'store_logo' => $data['storelogo'],
				'store_id'   => $result['store_id']
			);
		}

		
		$this->load->model('localisation/cancel_reason');

        $data['cancel_reasons'] = $this->model_localisation_cancel_reason->getCancelReasons();
        
		$data['action'] = $this->url->link('account/cancel/add', '', true);

		$data['track_link'] = $this->url->link('account/track_items', '', true);  

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/cancel_list', $data));
	}

	


	public function add() {
		$this->load->language('account/cancel');

		$this->load->model('account/return');
		$this->load->model('account/cancel');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			// $this->model_account_return->addReturn($this->request->post);

			// $this->response->redirect($this->url->link('account/return/success', '', true));
			$this->load->model('account/order');
            
			$dataLenght = count($this->request->post);
			$keys = array_keys($this->request->post);
			$product_ids = [];
			$order_ids = [];
			//$orderProduct = [];
			foreach($keys as $id){
				
				if (strpos($id, 'method') !== false) {
					
					$product_order_id = explode("method",$id);
					$product_ids[] = $product_order_id[0];
					$order_ids[] = $product_order_id[1];
                    
				}
			}
			
			// var_dump($this->request->post);
			// var_dump($product_ids);
			// var_dump($order_ids);
			// return;
			$i = 0;
			foreach($order_ids as $order_id){
            
		    $order_info = $this->model_account_order->getOrder($order_id);

			//var_dump($order_info);
			$data['order_id'] = $order_info['order_id'];
			$data['firstname'] = $order_info['firstname'];
			$data['lastname'] = $order_info['lastname'];
			$data['telephone'] = $order_info['telephone'];
			$data['email'] = $order_info['email']; 
			$data['date_ordered'] = $order_info['date_added'];

			$products = $this->model_account_order->getOrderProducts($order_id);
          
			foreach ($products as $product) {

				if($product['product_id'] == $product_ids[$i]){
				$data['product_id'] = $product['product_id'];
				$data['product'] = $product['name'];
				$data['model'] = $product['model'];
				$data['quantity'] = $product['quantity'];
				$data['cancel_reason_id'] = $this->request->post[$product['product_id']."reason"];
				$data['request_type'] = $this->request->post[$product['product_id']."method".$order_id];
				$this->model_account_cancel->addCancel($data);
				$this->model_account_cancel->updateProductStatus($data);
				
				//$this->sendMail($order_info['email'], $product['name']);
				
				$this->model_account_cancel->updateProductInventory($data);
				
				}
			}
			$i++;
		}	
			$this->response->redirect($this->url->link('account/cancel/success', '', true));
		}


		$this->response->redirect($this->url->link('account/cancel', 'error='.$this->error["form"] , true));

		
}

	protected function validate() {


		if (count($this->request->post) == 0 ) {
			$this->error['form'] = $this->language->get('error_selected'); 
		}

		// if (!$this->request->post['order_id']) {
		// 	$this->error['order_id'] = $this->language->get('error_order_id');
		// }

		// if (!isset($this->request->post['selected'])) {
		// 	$this->error['selected'] = $this->language->get('error_selected');
		// }

		

		// if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
		// 	$this->error['firstname'] = $this->language->get('error_firstname');
		// }

		// if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
		// 	$this->error['lastname'] = $this->language->get('error_lastname');
		// }

		// if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
		// 	$this->error['email'] = $this->language->get('error_email');
		// }

		// if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
		// 	$this->error['telephone'] = $this->language->get('error_telephone');
		// }

		// if ((utf8_strlen($this->request->post['product']) < 1) || (utf8_strlen($this->request->post['product']) > 255)) {
		// 	$this->error['product'] = $this->language->get('error_product');
		// }

		// if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
		// 	$this->error['model'] = $this->language->get('error_model');
		// }

		// if (empty($this->request->post['return_reason_id'])) {
		// 	$this->error['reason'] = $this->language->get('error_reason');
		// }

		// if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('return', (array)$this->config->get('config_captcha_page'))) {
		// 	$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

		// 	if ($captcha) {
		// 		$this->error['captcha'] = $captcha;
		// 	}
		// }

		// if ($this->config->get('config_return_id')) {
		// 	$this->load->model('catalog/information');

		// 	$information_info = $this->model_catalog_information->getInformation($this->config->get('config_return_id'));

		// 	if ($information_info && !isset($this->request->post['agree'])) {
		// 		$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
		// 	}
		// }

		return !$this->error;
	}

	public function success() {
		$this->load->language('account/cancel');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/store')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_cancel'),
			'href' => $this->url->link('account/cancel', '', true)
		);

		$data['continue'] = $this->url->link('common/store');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['homepageheader'] = $this->load->controller('common/homepageheader');

		$this->response->setOutput($this->load->view('common/success_cancel', $data));
	}

	public function sendMail($emailId, $product_name){

		$this->load->language('mail/forgotten');

		// $data['text_greeting'] = sprintf($this->language->get('text_greeting'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		// $data['text_change'] = $this->language->get('text_change');
		// $data['text_ip'] = $this->language->get('text_ip');
		
		// $data['reset'] = str_replace('&amp;', '&', $this->url->link('account/reset', 'code=' . $args[1], true));
		// $data['ip'] = $this->request->server['REMOTE_ADDR'];
		
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($emailId);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8'));
		$mail->setText($product_name . "has been requested for cancel.");
		$mail->send();

	}
}
