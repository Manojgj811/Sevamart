<?php
class ControllerAccountTrackItems extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {

			$this->session->data['redirect'] = $this->url->link('account/track_items', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		
		}

		$this->load->language('account/track_items');

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
			'text' => $this->language->get('heading_title_track_items'),
			'href' => $this->url->link('account/track_items', $url, true)
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
		$this->load->model('account/track_items');
		$this->load->model('setting/setting'); 
		$this->load->model('tool/image');

		$returns = $this->model_account_track_items->getReturn();
		$cancel = $this->model_account_track_items->getCancel();
		$item_not_received = $this->model_account_track_items->getItemNotReceived();
        $track_items = array_merge($returns, $cancel, $item_not_received);

	   array_multisort(array_column($track_items, "date_added"), SORT_DESC, $track_items);
     
       foreach ($track_items as $track_item) {

			    $productImage =  $this->model_account_order->getProductImage($track_item['product_id']);
			    
				$data['Images'] = $this->model_tool_image->resize($productImage['image'], 120, 75);
				
				$amount = $this->model_account_track_items->getOrderProductPrice($track_item['order_id'], $track_item['product_id']);
							$data['productsList'][] = array(
						    'store_id' =>  $track_item['store_id'],		
						    'order_id'   => $track_item['order_id'],			
							'product_id' => $track_item['product_id'],
							'status' => $track_item['status'],
							'image' => $data['Images'],
							'name' => $track_item['product'],
							'quantity' => $track_item['quantity'],
							'total' =>(int)$amount['total'] + (int)$amount['tax'],
							'requested_action' => $track_item['request_type'],
							'date_added' => $track_item['date_added'],
						);
        }

		$output = array_intersect_key(
			$track_items, 
			array_unique(array_map(function($item) {
				return $item['store_id'];
			}, $track_items))
		);


		foreach ($output as $result) {

			$logoresult=$this->model_setting_setting->getSetting('config', $result['store_id']);
            if (is_file(DIR_IMAGE . $logoresult['config_logo'])) 
                {
                    $data['storelogo'] = $this->model_tool_image->resize($logoresult['config_logo'], 120, 75);
                } 

			$data['stores'][] = array(
				
				'store_name' =>  $result['store_name'],
				'store_logo' => $data['storelogo'],
				'store_id'   => $result['store_id']
			);
		}

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['track_link'] = $this->url->link('account/track_items', '', true); 
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/track_items', $data));
	}

	



	public function add() {
		$this->load->language('account/item_not_received');

		$this->load->model('account/return');
		$this->load->model('account/item_not_received');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			// $this->model_account_return->addReturn($this->request->post);

			// $this->response->redirect($this->url->link('account/return/success', '', true));
			$this->load->model('account/order');
            
			$dataLenght = count($this->request->post);
			$keys = array_keys($this->request->post);
			$product_ids = [];
			$order_ids = [];
			foreach($keys as $id){
				
				if (strpos($id, 'method') !== false) {
					
					$product_order_id = explode("method",$id);
					$product_ids[] = $product_order_id[0];
					$order_ids[] = $product_order_id[1];

				}
			}
			
			//var_dump($this->request->post);
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
				$data['request_type'] = $this->request->post[$product['product_id']."method".$order_id];
				$this->model_account_item_not_received->addItemNotReceived($data);
				$this->model_account_item_not_received->updateProductStatus($data);
				
				}
			}
			$i++;
		}	
			$this->response->redirect($this->url->link('account/item_not_received/success', '', true));
		}

		$this->document->setTitle($this->language->get('heading_title_return'));
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

			

		if (isset($this->error['selected'])) {
			$data['error_warning'] = $this->error['selected'];
		} else {
			$data['error_warning'] = '';
		}

		// if (isset($this->error['order_id'])) {
		// 	$data['error_order_id'] = $this->error['order_id'];
		// } else {
		// 	$data['error_order_id'] = '';
		// }

		// if (isset($this->error['firstname'])) {
		// 	$data['error_firstname'] = $this->error['firstname'];
		// } else {
		// 	$data['error_firstname'] = '';
		// }

		// if (isset($this->error['lastname'])) {
		// 	$data['error_lastname'] = $this->error['lastname'];
		// } else {
		// 	$data['error_lastname'] = '';
		// }

		// if (isset($this->error['email'])) {
		// 	$data['error_email'] = $this->error['email'];
		// } else {
		// 	$data['error_email'] = '';
		// }

		// if (isset($this->error['telephone'])) {
		// 	$data['error_telephone'] = $this->error['telephone'];
		// } else {
		// 	$data['error_telephone'] = '';
		// }

		// if (isset($this->error['product'])) {
		// 	$data['error_product'] = $this->error['product'];
		// } else {
		// 	$data['error_product'] = '';
		// }

		// if (isset($this->error['model'])) {
		// 	$data['error_model'] = $this->error['model'];
		// } else {
		// 	$data['error_model'] = '';
		// }

		// if (isset($this->error['reason'])) {
		// 	$data['error_reason'] = $this->error['reason'];
		// } else {
		// 	$data['error_reason'] = '';
		// }

		$data['action'] = $this->url->link('account/return/add', '', true);


		if (isset($this->request->post['order_id'])) {
			$data['order_id'] = $this->request->post['order_id'];
		}
		else {
			$data['order_id'] = '';
		}

		
		$this->load->model('account/order');

		$order_info = $this->model_account_order->getOrder($this->request->post['order_id']);

		if ($order_info) {
			$this->document->setTitle($this->language->get('text_order'));

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->post['page'];
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title_return'),
				'href' => $this->url->link('account/return', $url, true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_return'),
				'href' => $this->url->link('account/return/info', 'order_id=' . $this->request->post['order_id'] . $url, true)
			);

			// if (isset($this->session->data['error'])) {
			// 	$data['error_warning'] = $this->session->data['error'];

			// 	unset($this->session->data['error']);
			// } else {
			// 	$data['error_warning'] = '';
			// }

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			if ($order_info['invoice_no']) {
				$data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$data['invoice_no'] = '';
			}

			$data['order_id'] = $this->request->post['order_id'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
			);

			$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$data['payment_method'] = $order_info['payment_method'];

			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']
			);

			$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$data['shipping_method'] = $order_info['shipping_method'];

			$this->load->model('catalog/product');
			$this->load->model('tool/upload');

			// Products
			$data['products'] = array();

			$products = $this->model_account_order->getOrderProducts($this->request->post['order_id']);
          
			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_account_order->getOrderOptions($this->request->post['order_id'], $product['order_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
  
				if ($product_info) {
					$reorder = $this->url->link('account/order/reorder', 'order_id=' . $order_id . '&order_product_id=' . $product['order_product_id'], true);
				} else {
					$reorder = '';
				}

				$data['products'][] = array(
			
					'product_id' => $product['product_id'],
					'name'     => $product['name'],
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					//'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					//'reorder'  => $reorder,
					//'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
				);
			}
		
		
		


		$data['back'] = $this->url->link('account/return', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/return_info', $data));
	}
	else {
		return new Action('error/not_found');
	}
}

	protected function validate() {
		if (!$this->request->post['order_id']) {
			$this->error['order_id'] = $this->language->get('error_order_id');
		}

		if (!isset($this->request->post['selected'])) {
			$this->error['selected'] = $this->language->get('error_selected');
		}

		

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
		$this->load->language('account/item_not_received');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/store')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_item_not_received'),
			'href' => $this->url->link('account/item_not_received', '', true)
		);

		$data['continue'] = $this->url->link('common/store');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}
