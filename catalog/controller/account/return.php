<?php
class ControllerAccountReturn extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/return', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

         
		if(isset($this->request->get['error'])){
			$data['error_warning'] = $this->request->get['error'];
		}else{
			$data['error_warning'] = "";
		}

		$this->load->language('account/order');

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
			'href' => $this->url->link('account/return', $url, true)
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

		$data['open_request'] = array();
 		$return_total = $this->model_account_return->getTotalReturns();
      
		$results = $this->model_account_return->getRequestedReturn(($page2 - 1) * 10 , 10);

		// $output = array_intersect_key(
		// 	$results, 
		// 	array_unique(array_map(function($item) {
		// 		return $item['order_id'];
		// 	}, $results))
		// );

		// foreach ($output as $result) {
		// 	$data['open_request'][] = array(
		// 		'return_id'  => $result['return_id'],
		// 		'order_id'   => $result['order_id'],
		// 		'name'       => $result['firstname'] . ' ' . $result['lastname'],
		// 		'status'     => $result['status'],
		// 		'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
		// 		'href'       => $this->url->link('account/return/openRequest', 'order_id=' . $result['order_id'] . $url, true)
		// 	);
		// }
	
		
		$pagination2 = new Pagination();
		$pagination2->total = $return_total;
		$pagination2->page = $page2;
		$pagination2->limit = 10;
		$pagination2->url = $this->url->link('account/return', 'page2={page}', true);

		 $data['pagination2'] = $pagination2->render();

		 $data['results2'] = sprintf($this->language->get('text_pagination'), ($return_total) ? (($page2 - 1) * $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit')) + 1 : 0, ((($page2 - 1) * $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit')) > ($return_total - $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'))) ? $return_total : ((($page2 - 1) * $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit')) + $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit')), $return_total, ceil($return_total / $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit')));

		// $data['continue'] = $this->url->link('account/account', '', true);

		// $data['column_left'] = $this->load->controller('common/column_left');
		// $data['column_right'] = $this->load->controller('common/column_right');
		// $data['content_top'] = $this->load->controller('common/content_top');
		// $data['content_bottom'] = $this->load->controller('common/content_bottom');
		// $data['footer'] = $this->load->controller('common/footer');
		// $data['header'] = $this->load->controller('common/header');


		$data['returns'] = array();

		$this->load->model('account/order');
		$this->load->model('setting/setting'); 
		$this->load->model('tool/image');

		$order_total = $this->model_account_order->getTotalDeliveredOrders();

		$results = $this->model_account_order->getOrders();
		$data['productsList'] = [];
       foreach ($results as $result) {

		  if($result['status'] == "Delivered"){

			$products = $this->model_account_order->getOrderProducts($result['order_id']);

		     foreach ($products as $product){

				    $product_status_id =  $this->model_account_return->getProductStatus($result['order_id'], $product['product_id']);

				if($product_status_id == 2){

					$requestedDate = $this->model_account_return->getRequestedDate($result['order_id'], $product['product_id']);
				}
				else{

					$requestedDate['request_type'] = '';
					$requestedDate['date_added'] = '';
				}
			    $productImage = $this->model_account_order->getProductImage($product['product_id']);
			    
				$data['Images'] = $this->model_tool_image->resize($productImage['image'], 120, 75);
				 
							$data['productsList'][] = array(
						    'store_id' =>  $result['store_id'],	
							'store_name' => $result['store_name'],
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

    
		$newArray = array_filter($data['productsList'], function ($var) {
			return ($var['status'] == 'Delivered' and ($var['product_status'] == "7" or $var['product_status'] == "2") );
		});

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

	//	var_dump($data['stores']);

		$results = $this->model_account_return->getReturns(($page - 1) * 10, 10);
       
		
		foreach ($results as $result) {
			$data['returns'][] = array(
				'return_id'  => $result['return_id'],
				'order_id'   => $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'href'       => $this->url->link('account/return/info', 'return_id=' . $result['return_id'] . $url, true)
			);
		}
        
		//var_dump($data['returns']);
		// foreach ($results as $result) {
		// 	$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
		// 	$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

		// 	$data['returns'][] = array(
		// 		'order_id'   => $result['order_id'],
		// 		'store_name' => $result['store_name'],
		// 		'name'       => $result['firstname'] . ' ' . $result['lastname'],
		// 		'status'     => $result['status'],
		// 		'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
		// 		'products'   => ($product_total + $voucher_total),
		// 		'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
		// 		'view'       => $this->url->link('account/return/info', 'order_id=' . $result['order_id'], true),
		// 	);
		// }
		
		// $pagination = new Pagination();
		// $pagination->total = $order_total;
		// $pagination->page = $page;
		// $pagination->limit = 10;
		// $pagination->url = $this->url->link('account/return', 'page={page}', true);

		// $data['pagination'] = $pagination->render();

		// $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));

		$this->load->model('localisation/return_reason');

        $data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();
        
		$data['action'] = $this->url->link('account/return/add', '', true);
    
		$data['track_link'] = $this->url->link('account/track_items', '', true); 

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/return_list', $data));
	}

	public function info() {
		$this->load->language('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}


		// if (!$this->customer->isLogged()) {
		// 	$this->session->data['redirect'] = $this->url->link('account/return/info', 'return_id=' . $return_id, true);

		// 	$this->response->redirect($this->url->link('account/login', '', true));
		// }

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/return/info', 'order_id=' . $order_id, true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		// $this->load->model('account/return');

		// $return_info = $this->model_account_return->getReturn($return_id);

		// if ($return_info) {
		// 	$this->document->setTitle($this->language->get('text_return'));

		// 	$data['breadcrumbs'] = array();

		// 	$data['breadcrumbs'][] = array(
		// 		'text' => $this->language->get('text_home'),
		// 		'href' => $this->url->link('common/store', '', true)
		// 	);

		// 	$data['breadcrumbs'][] = array(
		// 		'text' => $this->language->get('text_account'),
		// 		'href' => $this->url->link('account/account', '', true)
		// 	);

		// 	$url = '';

		// 	if (isset($this->request->get['page'])) {
		// 		$url .= '&page=' . $this->request->get['page'];
		// 	}

		// 	$data['breadcrumbs'][] = array(
		// 		'text' => $this->language->get('heading_title_return'),
		// 		'href' => $this->url->link('account/return', $url, true)
		// 	);

		// 	$data['breadcrumbs'][] = array(
		// 		'text' => $this->language->get('text_return'),
		// 		'href' => $this->url->link('account/return/info', 'return_id=' . $this->request->get['return_id'] . $url, true)
		// 	);

		// 	$data['return_id'] = $return_info['return_id'];
		// 	$data['order_id'] = $return_info['order_id'];
		// 	$data['date_ordered'] = date($this->language->get('date_format_short'), strtotime($return_info['date_ordered']));
		// 	$data['date_added'] = date($this->language->get('date_format_short'), strtotime($return_info['date_added']));
		// 	$data['firstname'] = $return_info['firstname'];
		// 	$data['lastname'] = $return_info['lastname'];
		// 	$data['email'] = $return_info['email'];
		// 	$data['telephone'] = $return_info['telephone'];
		// 	$data['product'] = $return_info['product'];
		// 	$data['model'] = $return_info['model'];
		// 	$data['quantity'] = $return_info['quantity'];
		// 	$data['reason'] = $return_info['reason'];
		// 	$data['opened'] = $return_info['opened'] ? $this->language->get('text_yes') : $this->language->get('text_no');
		// 	$data['comment'] = nl2br($return_info['comment']);
		// 	$data['action'] = $return_info['action'];

		// 	$data['histories'] = array();

		// 	$results = $this->model_account_return->getReturnHistories($this->request->get['return_id']);

		// 	foreach ($results as $result) {
		// 		$data['histories'][] = array(
		// 			'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
		// 			'status'     => $result['status'],
		// 			'comment'    => nl2br($result['comment'])
		// 		);
		// 	}

		// 	$data['continue'] = $this->url->link('account/return', $url, true);

		// 	$data['column_left'] = $this->load->controller('common/column_left');
		// 	$data['column_right'] = $this->load->controller('common/column_right');
		// 	$data['content_top'] = $this->load->controller('common/content_top');
		// 	$data['content_bottom'] = $this->load->controller('common/content_bottom');
		// 	$data['footer'] = $this->load->controller('common/footer');
		// 	$data['header'] = $this->load->controller('common/header');

		// 	$this->response->setOutput($this->load->view('account/return_info', $data));
		// } else {
		// 	$this->document->setTitle($this->language->get('text_return'));

		// 	$data['breadcrumbs'] = array();

		// 	$data['breadcrumbs'][] = array(
		// 		'text' => $this->language->get('text_home'),
		// 		'href' => $this->url->link('common/store')
		// 	);

		// 	$data['breadcrumbs'][] = array(
		// 		'text' => $this->language->get('text_account'),
		// 		'href' => $this->url->link('account/account', '', true)
		// 	);

		// 	$data['breadcrumbs'][] = array(
		// 		'text' => $this->language->get('heading_title_return'),
		// 		'href' => $this->url->link('account/return', '', true)
		// 	);

		// 	$url = '';

		// 	if (isset($this->request->get['page'])) {
		// 		$url .= '&page=' . $this->request->get['page'];
		// 	}

		// 	$data['breadcrumbs'][] = array(
		// 		'text' => $this->language->get('text_return'),
		// 		'href' => $this->url->link('account/return/info', 'return_id=' . $return_id . $url, true)
		// 	);

		// 	$data['continue'] = $this->url->link('account/return', '', true);

		// 	$data['column_left'] = $this->load->controller('common/column_left');
		// 	$data['column_right'] = $this->load->controller('common/column_right');
		// 	$data['content_top'] = $this->load->controller('common/content_top');
		// 	$data['content_bottom'] = $this->load->controller('common/content_bottom');
		// 	$data['footer'] = $this->load->controller('common/footer');
		// 	$data['header'] = $this->load->controller('common/header');

		// 	$this->response->setOutput($this->load->view('error/not_found', $data));
		// }

		$this->load->model('account/order');

		$order_info = $this->model_account_order->getOrder($order_id);

		if ($order_info) {
			$this->document->setTitle($this->language->get('text_order'));

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
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
				'href' => $this->url->link('account/return/info', 'order_id=' . $this->request->get['order_id'] . $url, true)
			);

			if (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

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

			$data['order_id'] = $this->request->get['order_id'];
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

			$products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);
          
			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

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

			// Voucher
			// $data['vouchers'] = array();

			// $vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);

			// foreach ($vouchers as $voucher) {
			// 	$data['vouchers'][] = array(
			// 		'description' => $voucher['description'],
			// 		'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
			// 	);
			// }

			// Totals
			// $data['totals'] = array();

			// $totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);

			// foreach ($totals as $total) {
			// 	$data['totals'][] = array(
			// 		'title' => $total['title'],
			// 		'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
			// 	);
			// } 

			//$data['comment'] = nl2br($order_info['comment']);

			// History
			// $data['histories'] = array();

			// $results = $this->model_account_order->getOrderHistories($this->request->get['order_id']);

			// foreach ($results as $result) {
			// 	$data['histories'][] = array(
			// 		'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
			// 		'status'     => $result['status'],
			// 		'comment'    => $result['notify'] ? nl2br($result['comment']) : ''
			// 	);
			// }
			$this->load->model('localisation/return_reason');

			$data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();





            $data['action'] = $this->url->link('account/return/add', '', true);

			$data['back'] = $this->url->link('account/return', '', true);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('account/return_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}

	public function openRequest() {
		$this->load->language('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}


		// if (!$this->customer->isLogged()) {
		// 	$this->session->data['redirect'] = $this->url->link('account/return/info', 'return_id=' . $return_id, true);

		// 	$this->response->redirect($this->url->link('account/login', '', true));
		// }

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/return/openRequest', 'order_id=' . $order_id, true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}


		$this->load->model('account/return');

		//$order_info = $this->model_account_order->getOrder($order_id);

			$this->document->setTitle($this->language->get('text_order'));

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
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
				'href' => $this->url->link('account/return/info', 'order_id=' . $this->request->get['order_id'] . $url, true)
			);

			if (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			

			$data['order_id'] = $this->request->get['order_id'];
			//$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

			
			


			$this->load->model('catalog/product');
			$this->load->model('tool/upload');

			// Products
			$data['products'] = array();

			$products = $this->model_account_return->getOpenReturns($this->request->get['order_id']);
			
			foreach ($products as $product) {
				
				

				$data['products'][] = array(
			
					'return_id' => $product['return_id'],
					'firstname' => $product['firstname'],
					'product'   => $product['product'],
					'quantity' => $product['quantity'],
					'reason' => $product['reason'],
					'status' => $product['status'],
					'request_type' => $product['request_type'],
					//'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					//'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					//'reorder'  => $reorder,
					//'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
				);
			}
			// var_dump($data['products']);
            // return;
			// Voucher
			// $data['vouchers'] = array();

			// $vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);

			// foreach ($vouchers as $voucher) {
			// 	$data['vouchers'][] = array(
			// 		'description' => $voucher['description'],
			// 		'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
			// 	);
			// }

			// Totals
			// $data['totals'] = array();

			// $totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);

			// foreach ($totals as $total) {
			// 	$data['totals'][] = array(
			// 		'title' => $total['title'],
			// 		'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
			// 	);
			// } 

			//$data['comment'] = nl2br($order_info['comment']);

			// History
			// $data['histories'] = array();

			// $results = $this->model_account_order->getOrderHistories($this->request->get['order_id']);

			// foreach ($results as $result) {
			// 	$data['histories'][] = array(
			// 		'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
			// 		'status'     => $result['status'],
			// 		'comment'    => $result['notify'] ? nl2br($result['comment']) : ''
			// 	);
			// }
			$this->load->model('localisation/return_reason');

			$data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();





            //$data['action'] = $this->url->link('account/return/add', '', true);

			$data['back'] = $this->url->link('account/return', '', true);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('account/return_open_request_list', $data));
		// } else {
		// 	return new Action('error/not_found');
		// }
	}



	public function add() {
		$this->load->language('account/order');

		$this->load->model('account/return');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			//$this->model_account_return->addReturn($this->request->post);
           // return;
			//$this->response->redirect($this->url->link('account/return/success', '', true));

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
          
			foreach ($products as $product){
				if($product['product_id'] == $product_ids[$i]){
				$data['product_id'] = $product['product_id'];
				$data['product'] = $product['name'];
				$data['model'] = $product['model'];
				$data['quantity'] = $product['quantity'];
				$data['return_reason_id'] = $this->request->post[$product['product_id']."reason"];
				$data['opened'] = 0;
				$data['comment'] = "";
				$data['request_type'] = $this->request->post[$product['product_id']."method".$order_id];
				$this->model_account_return->addReturn($data);
				$this->model_account_return->updateProductStatus($data);

				//$this->sendMail($order_info['email'], $product['name']);


              if($data['request_type'] == "Refund"){

					    $this->model_account_return->updateProductInventory($data);
						
				    }
				}
			}
            $i++;
		}	

			$this->response->redirect($this->url->link('account/return/success', '', true));

		}

		$this->response->redirect($this->url->link('account/return', 'error='.$this->error["form"] , true));

	}
	

	protected function validate() {
		
		if (count($this->request->post) == 0 ) {
			$this->error['form'] = $this->language->get('error_selected'); 
		}

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
		$this->load->language('account/return');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/store')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/return', '', true)
		);

		$data['continue'] = $this->url->link('common/store');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['homepageheader'] = $this->load->controller('common/homepageheader');

		$this->response->setOutput($this->load->view('common/success_return', $data));
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
		$mail->setText($product_name . "has been requested for return.");
		$mail->send();

	}
}
