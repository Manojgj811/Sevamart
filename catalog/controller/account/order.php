<?php
class ControllerAccountOrder extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/order');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/store')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/order', $url, true)
		);

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['orders'] = array();

		$this->load->model('account/order');

		$order_total = $this->model_account_order->getTotalOrders();

		$results = $this->model_account_order->getOrders(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
			$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);
			
			$data['orders'][] = array(
				'order_id'   => $result['order_id'],
				'store_name' => $result['store_name'],
				'order_method' => $result['order_method'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'products'   => ($product_total + $voucher_total),
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'view'       => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], true),
			);
		}
		//var_dump($data['orders']);
		
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('account/order', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/order_list', $data));
	}

	public function info() {
		$this->load->language('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order/info', 'order_id=' . $order_id, true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}
		
		$this->load->model('account/order');
		$this->load->model('setting/setting');
		$this->load->model('setting/store');
		$this->load->model('storecontract/storecontract');

		$order_info = $this->model_account_order->getOrder($order_id);

		$ordertotalamount=$order_info['total'];

		// var_dump($ordertotalamount);
		// echo "<br>";
   $orderpickup=$order_info['pickup'];

 	 //var_dump($orderpickup);
		
	 //var_dump($order_info);

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
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('account/order', $url, true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_order'),
				'href' => $this->url->link('account/order/info', 'order_id=' . $this->request->get['order_id'] . $url, true)
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

			if ($order_info['order_method']) {
				$data['order_method'] = $order_info['order_method'];
			} else {
				$data['order_method'] = '';
			}

			$data['order_id'] = $this->request->get['order_id'];
			//$data['store_name'] = $this->request->get['store_name'];
			if ($order_info['store_name']) {
				$data['store_name'] = $order_info['store_name'] ;
			} else {
				$data['store_name'] = '';
			}
			//var_dump($data['store_name']);

			$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

				if ($store_info) {
					$data['store_address'] = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
				} else {
					$data['store_address'] = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
				}

				//var_dump($data['store_address']);

				$store_gstin = $this->model_storecontract_storecontract->getStoreSubscription('config', $order_info['store_id']);

				if ($store_gstin) {
					$data['store_GSTIN'] = $store_gstin['GSTIN'];
					
				} else {
					$data['store_GSTIN'] = $this->config->get('GSTIN');
					
				}
				//var_dump($data['store_GSTIN']);

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

			if($orderpickup==0)
			{
			$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			}
			$data['shipping_method'] = $order_info['shipping_method'];

			$this->load->model('catalog/product');
			$this->load->model('tool/upload');

			// Products
			$data['products'] = array();

			$products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);
       
			$rate_sum = 0;
			$sgst=0;
			$deliverybillamount='';
				/////////////// for grand total 
					
				$totalamountgrandtotal=0;

				$totalamountincgst=0;
	
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
					'reorder'  => $reorder,
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'] ,$this->session->data['currency']),
				//'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'      => $this->currency->format($product['price']  * $product['quantity'] , $this->session->data['currency']),
					'cgstrate'=>$product['cgstrate'],
					'sgstrate'=>$product['sgstrate'],

                    $cgsttotalamount=($product['price']*$product['quantity']/100)*$product['cgstrate'],

					$sgsttotalamount=($product['price']*$product['quantity']/100)*$product['sgstrate'],

					$deliverybillamount=$product['deliverycharge'],

					'cgstamount'=>$cgsttotalamount,
				
			    	'sgstamount'=>$sgsttotalamount,
				
					$totalamountgrandtotal+=$product['total'],
				  
                   $rate_sum += $cgsttotalamount,

					  $sgst+=$sgsttotalamount,

                 	  'totalgst'=>($product['price']*$product['quantity'])+$cgsttotalamount+$sgsttotalamount,
					   $totalamountincgst+=($product['price']*$product['quantity'])+$cgsttotalamount+$sgsttotalamount,

					'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true),
					'cancel'   => $this->url->link('account/cancel', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
				);
				$data['totalamountgrandtotal']=$totalamountgrandtotal;

				$data['cgstgrandtotal']=$rate_sum;
		
				
				$data['sgstgrandtotal']=$sgst;
				
				$data['gstgrandtotal']=$totalamountincgst;
			}

			//echo "the sum of  sgsttotalamount is $sgst";
			//echo "the sum of  cgsttotalamount is $rate_sum";
			// Voucher
			$data['vouchers'] = array();

			$vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);

			foreach ($vouchers as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			// Totals

			if($orderpickup==1)
			{

			$data['totals']=array();

			$totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);


			foreach ($totals as $total) 
			{
				if($total['code']=='sub_total')
				{
				$data['subtotals'][] = array(
					'title' => $total['title'] .'(ex GST);',
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
				);
				}
              	
			}


			$data['cgsttotalsum'][]=array(
				'title'=>'CGST Amount',
				'cgstvalue'=>   $this->currency->format($rate_sum,$this->session->data['currency'])
                 	);

           	//////////for total product sgst amount 

			$data['sgsttotalsum'][]=array(
				'title'=>'SGST Amount',
				'sgstvalue'=>   $this->currency->format($sgst,$this->session->data['currency'])
                 	);

		/////////  for 	 ordertotalamount

		$data['GrandTotal'][]=array(
			'title'=>'GrandTotal(inc GST)',
			'sumtotal'=>  $this->currency->format($ordertotalamount,$this->session->data['currency'])
				 );


			}

////////////////////////////////////////

		if($orderpickup==0)
		 {

		// echo "pick up not one";

		$data['totals'] = array();

          $totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);

		////for sub total
		foreach ($totals as $total)
		 {
			if($total['code']=='sub_total')
			{
			$data['subtotals'][] = array(
				'title' => $total['title'].'(ex GST):',
				'text'  => $this->currency->format($total['value']+$deliverybillamount, $order_info['currency_code'], $order_info['currency_value'])
			);
			}

	      }

			 ///////// to display delivery  charge in order invoice bill

					 $ordertaxproducts=$this->model_account_order->getOrderTax($this->request->get['order_id']);

					 // var_dump( $ordertaxproducts);
		 
					  foreach($ordertaxproducts  as $taxvalue)
					  {
						  $data['deliverybillamount'][]=array(
						 'title'=>'Shipping Charges(ex GST):',
		 		 
					 'cgsttitle'=>'CGST Amount on shipping(@'. $taxvalue['cgst_delivery_percentage'] .'%):',
					 'sumdeliverytotal'=>  $this->currency->format($deliverybillamount,$this->session->data['currency']),
					 'cgstsumdeliverytotal'=>  $this->currency->format($taxvalue['cgst_delivery_amount'],$this->session->data['currency']),
					 'sgsttitle'=>'SGST Amount on shipping(@'. $taxvalue['sgst_delivery_percentage'] .'%):',
					 'sgstsumdeliverytotal'=>  $this->currency->format($taxvalue['sgst_delivery_amount'],$this->session->data['currency'])
		
						 );
					  }

				 $data['cgsttotalsum'][]=array(
				'title'=>'CGST Amount',
				'cgstvalue'=>   $this->currency->format($rate_sum+$taxvalue['cgst_delivery_amount'],$this->session->data['currency'])
                 	);

           	//////////for total product sgst amount 

			$data['sgsttotalsum'][]=array(
				'title'=>'SGST Amount',
				'sgstvalue'=>   $this->currency->format($sgst+$taxvalue['sgst_delivery_amount'],$this->session->data['currency'])
                 	);

		/////////  for 	 ordertotalamount

		$data['GrandTotal'][]=array(
			'title'=>'GrandTotal(inc GST)',
			'sumtotal'=>  $this->currency->format($ordertotalamount,$this->session->data['currency'])
				 );


				}

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}

			$data['comment'] = nl2br($order_info['comment']);

			// History
			$data['histories'] = array();

			$results = $this->model_account_order->getOrderHistories($this->request->get['order_id']);

			foreach ($results as $result) {
				$data['histories'][] = array(
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'status'     => $result['status'],
					'comment'    => $result['notify'] ? nl2br($result['comment']) : ''
				);
			}

			$order_status = $this->model_account_order->getOrder($this->request->get['order_id']);
            $data['order_status_id'] = $order_status['order_status_id'];
			
            //var_dump();
			$data['continue'] = $this->url->link('account/order', '', true);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('account/order_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}

	public function reorder() {
		$this->load->language('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$this->load->model('account/order');

		$order_info = $this->model_account_order->getOrder($order_id);

		if ($order_info) {
			if (isset($this->request->get['order_product_id'])) {
				$order_product_id = $this->request->get['order_product_id'];
			} else {
				$order_product_id = 0;
			}

			$order_product_info = $this->model_account_order->getOrderProduct($order_id, $order_product_id);

			if ($order_product_info) {
				$this->load->model('catalog/product');

				$product_info = $this->model_catalog_product->getProduct($order_product_info['product_id']);

				if ($product_info) {
					$option_data = array();

					$order_options = $this->model_account_order->getOrderOptions($order_product_info['order_id'], $order_product_id);

					foreach ($order_options as $order_option) {
						if ($order_option['type'] == 'select' || $order_option['type'] == 'radio' || $order_option['type'] == 'image') {
							$option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'checkbox') {
							$option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
							$option_data[$order_option['product_option_id']] = $order_option['value'];
						} elseif ($order_option['type'] == 'file') {
							$option_data[$order_option['product_option_id']] = $this->encryption->encrypt($this->config->get('config_encryption'), $order_option['value']);
						}
					}

					$this->cart->add($order_product_info['product_id'], $order_product_info['quantity'], $option_data);

					$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $product_info['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
				} else {
					$this->session->data['error'] = sprintf($this->language->get('error_reorder'), $order_product_info['name']);
				}
			}
		}

		$this->response->redirect($this->url->link('account/order/info', 'order_id=' . $order_id));
	}
}