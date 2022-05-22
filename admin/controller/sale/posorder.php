<?php
class ControllerSalePosOrder extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

		$this->getList();
	}

	public function invoice() {
		$this->load->language('sale/order');

		$data['title'] = $this->language->get('text_invoice');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$this->load->model('sale/order');

		$this->load->model('setting/setting');
		$this->load->model('storecontract/storecontract');

		$data['orders'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}

		foreach ($orders as $order_id) {
			$order_info = $this->model_sale_order->getOrder($order_id);

			$ordertotalamount=$order_info['total'];

			$orderpickup=$order_info['pickup'];

			//echo "the pickup val is $orderpickup ";


		//var_dump($ordertotalamount);

			if ($order_info) {
				$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
				} else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
				}

				if ($order_info['invoice_no']) {
					$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$invoice_no = '';
				}

				$store_gstin = $this->model_storecontract_storecontract->getStorecontract('config', $order_info['store_id']);

				if ($store_gstin) {
					$data['store_GSTIN'] = $store_gstin['GSTIN'];
					
					
				} else {
					$data['store_GSTIN'] = $this->config->get('GSTIN');
					
				}
				//var_dump($data['store_GSTIN']);

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

				$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

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
				$this->load->model('tool/upload');

				$product_data = array();

				$products = $this->model_sale_order->getOrderProducts($order_id);

				$rate_sum = 0;

		    	$sgst=0;

				$deliverybillamount='';
						
				$totalamountgrandtotal=0;

				$totalamountincgst=0;

				$c=0;

				foreach ($products as $product) {
					$option_data = array();

					$c=$product['total'];

					$options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

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
							'value' => $value
						);
					}

					$product_data[] = array(
						'name'     => $product['name'],
						'model'    => $product['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $this->currency->format($product['price'] ,$this->config->get('config_currency')),
			
						//'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						//'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
						'total'      => $this->currency->format($product['price']  * $product['quantity'] , $this->config->get('config_currency')),
						'cgstrate'=>$product['cgstrate'],
						'sgstrate'=>$product['sgstrate'],
						'deliverycharge'=>$product['deliverycharge'],

						$deliverybillamount=$product['deliverycharge'],

                        $cgsttotalamount=($product['price']*$product['quantity']/100)*$product['cgstrate'],

			         	$sgsttotalamount=($product['price']*$product['quantity']/100)*$product['sgstrate'],

					 'cgstamount'=>$cgsttotalamount,

					 $totalamountgrandtotal+=$product['total'],
				  
				  'sgstamount'=>$sgsttotalamount,

				  $rate_sum += $cgsttotalamount,

				  $sgst+=$sgsttotalamount,

				   'totalgst'=>($product['price']*$product['quantity'])+$cgsttotalamount+$sgsttotalamount,

				   $totalamountincgst+=($product['price']*$product['quantity'])+$cgsttotalamount+$sgsttotalamount,

                	);

					$data['totalamountgrandtotal']=$totalamountgrandtotal;

					$data['cgstgrandtotal']=$rate_sum;
			
					
					$data['sgstgrandtotal']=$sgst;
					
					$data['gstgrandtotal']=$totalamountincgst;


				}

				$voucher_data = array();

				$vouchers = $this->model_sale_order->getOrderVouchers($order_id);

				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$total_data = array();

				$d=gettype($total_data);
	
				$totals = $this->model_sale_order->getOrderTotals($order_id);

				
		 if($orderpickup==1)
			 {

			//	echo "yes pos ordered they WILL  PICK IT  from store";

				foreach ($totals as $total) 
				{
					if($total['code']=='sub_total')
					{
					$data['subtotals'][] = array(
						'title' => $total['title'] .'(ex GST):',
						'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
					);
					}
				}
			
					 ///////// to display delivery  charge in order invoice bill

					$ordertaxproducts=$this->model_sale_order->getOrderTax($this->request->get['order_id']);

				//	var_dump( $ordertaxproducts);
	
					

					   //////////for total product cgst amount 

			$data['cgsttotalsum'][]=array(
				'title'=>'CGST Amount',
				'cgstvalue'=> $this->currency->format($rate_sum,  $this->config->get('config_currency'))
                 	);

           	//////////for total product sgst amount 

			$data['sgsttotalsum'][]=array(
				'title'=>'SGST Amount',
				'sgstvalue'=>$this->currency->format($sgst,$this->config->get('config_currency') )
                 	);


		///////  for 	 ordertotalamount

		$data['GrandTotals'][]=array(
			'title'=>'GrandTotal(INC GST)',
			'sumtotal'=>  $this->currency->format($ordertotalamount,$this->config->get('config_currency'))
				 );

			}


	$data['backhome']= $this->url->link('sale/pos', 'user_token=' . $this->session->data['user_token'],true);


						$data['orders'][] = array(
							'order_id'	       => $order_id,
							'invoice_no'       => $invoice_no,
							'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
							'store_name'       => $order_info['store_name'],
							'store_url'        => rtrim($order_info['store_url'], '/'),
							'store_address'    => nl2br($store_address),
							'store_email'      => $store_email,
							'store_telephone'  => $store_telephone,
							'store_fax'        => $store_fax,
							'email'            => $order_info['email'],
							'telephone'        => $order_info['telephone'],
							'shipping_address' => @$shipping_address,
							'shipping_method'  => $order_info['shipping_method'],
							'payment_address'  => $payment_address,
							'payment_method'   => $order_info['payment_method'],
							'product'          => $product_data,
							'voucher'          => $voucher_data,
							'total'            => $total_data,
							'comment'          => nl2br($order_info['comment'])
						);

					}
				}

		$this->response->setOutput($this->load->view('sale/posorder_invoice', $data));
	}




	}
