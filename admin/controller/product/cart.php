<?php
class ControllerProductCart extends Controller {
	public function index() {

	$this->load->language('sale/cart');

		//$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		// $data['breadcrumbs'][] = array(
		// 	'href' => $this->url->link('common/store'),
		// 	'text' => $this->language->get('text_home')
		// );

		// $data['breadcrumbs'][] = array(
		// 	'href' => $this->url->link('checkout/cart'),
		// 	'text' => $this->language->get('heading_title')
		// );

		if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
			 
			
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			
          $this->load->model('tool/image');
			$this->load->model('tool/upload');

			// $data['products'] = array();

			// $products = $this->cart->getProducts();

			// //var_dump($products );

			// foreach ($products as $product) {
			// 	$product_total = 0;

			// 	foreach ($products as $product_2) {
			// 		if ($product_2['product_id'] == $product['product_id']) {
			// 			$product_total += $product_2['quantity'];
			// 		}
			// 	}

			// 	if ($product['minimum'] > $product_total) {
			// 		$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			// 	}

			// 	if ($product['image']) {
			// 		$image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
			// 	} else {
			// 		$image = '';
			// 	}

			// 	$option_data = array();

			// 	foreach ($product['option'] as $option) {
			// 		if ($option['type'] != 'file') {
			// 			$value = $option['value'];
			// 		} else {
			// 			$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

			// 			if ($upload_info) {
			// 				$value = $upload_info['name'];
			// 			} else {
			// 				$value = '';
			// 			}
			// 		}

			// 		$option_data[] = array(
			// 			'name'  => $option['name'],
			// 			'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
			// 		);
			// 	}

			// 	// Display prices
			// 	if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			// 		$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
					
			// 		$price = $this->currency->format($unit_price, $this->session->data['currency']);
			// 		$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
			// 	} else {
			// 		$price = false;
			// 		$total = false;
			// 	}

			// 	$recurring = '';

			// 	if ($product['recurring']) {
			// 		$frequencies = array(
			// 			'day'        => $this->language->get('text_day'),
			// 			'week'       => $this->language->get('text_week'),
			// 			'semi_month' => $this->language->get('text_semi_month'),
			// 			'month'      => $this->language->get('text_month'),
			// 			'year'       => $this->language->get('text_year')
			// 		);

			// 		if ($product['recurring']['trial']) {
			// 			$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
			// 		}

			// 		if ($product['recurring']['duration']) {
			// 			$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
			// 		} else {
			// 			$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
			// 		}
			// 	}

			// 	/// added newly product_id key to compare 
			// 	$data['products'][] = array(
			// 		'product_id'=>$product['product_id'],
			// 		'cart_id'   => $product['cart_id'],
			// 		'thumb'     => $image,
			// 		'name'      => $product['name'],
			// 		'model'     => $product['model'],
			// 		'option'    => $option_data,
			// 		'recurring' => $recurring,
			// 		'quantity'  => $product['quantity'],
			// 		'stock'     => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
			// 		'reward'    => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
			// 		'price'     => $price,
			// 		'total'     => ($total),
					
			// 		'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			// 	);
			// }

			// Gift Voucher
			// $data['vouchers'] = array();

			// if (!empty($this->session->data['vouchers'])) {
			// 	foreach ($this->session->data['vouchers'] as $key => $voucher) {
			// 		$data['vouchers'][] = array(
			// 			'key'         => $key,
			// 			'description' => $voucher['description'],
			// 			'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency']),
			// 			'remove'      => $this->url->link('checkout/cart', 'remove=' . $key)
			// 		);
			// 	}
			// }

			// Totals
			$this->load->model('setting/extension');

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;
			
			// Because __call can not keep var references so we put them into an array. 			
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);
			
			// Display prices
			// if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			// 	$sort_order = array();

			// 	$results = $this->model_setting_extension->getExtensions('total');

			// 	foreach ($results as $key => $value) {
			// 		$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			// 	}

			// 	array_multisort($sort_order, SORT_ASC, $results);

			// 	foreach ($results as $result) {
			// 		if ($this->config->get('total_' . $result['code'] . '_status')) {
			// 			$this->load->model('extension/total/' . $result['code']);
						
			// 			// We have to put the totals in an array so that they pass by reference.
			// 			$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
			// 		}
			// 	}

			// 	$sort_order = array();

			// 	foreach ($totals as $key => $value) {
			// 		$sort_order[$key] = $value['sort_order'];
			// 	}

			// 	array_multisort($sort_order, SORT_ASC, $totals);
			// }

			// $data['totals'] = array();

			// foreach ($totals as $total) {
			// 	$data['totals'][] = array(
			// 		'title' => $total['title'],
			// 		'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
			// 	);
			// }

			// $data['continue'] = $this->url->link('common/store');

			// $data['checkout'] = $this->url->link('checkout/checkout', '', true);

			// $this->load->model('setting/extension');

			// $data['modules'] = array();
			
			// $files = glob(DIR_APPLICATION . '/controller/extension/total/*.php');

			// if ($files) {
			// 	foreach ($files as $file) {
			// 		$result = $this->load->controller('extension/total/' . basename($file, '.php'));
					
			// 		if ($result) {
			// 			$data['modules'][] = $result;
			// 		}
			// 	}
			// }

			$data['column_left'] = $this->load->controller('common/column_left');
		
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/category', $data));
		
		} 
		else 
		{
			// $data['text_error'] = $this->language->get('text_empty');
			
			// $data['continue'] = $this->url->link('common/store');

			// unset($this->session->data['success']);

			// $data['column_left'] = $this->load->controller('common/column_left');
			// $data['column_right'] = $this->load->controller('common/column_right');
			// $data['content_top'] = $this->load->controller('common/content_top');
			// $data['content_bottom'] = $this->load->controller('common/content_bottom');
			// $data['footer'] = $this->load->controller('common/footer');
			// $data['header'] = $this->load->controller('common/header');

			// $this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function add() {

		$this->load->language('sale/cart');

		$json = array();

		if (isset($this->request->post['product_id'])) 
		{
			$product_id = (int)$this->request->post['product_id'];

			//var_dump($product_id);
    	} 
	
	 	else 
		{
			$product_id = 0;
		}

		if (isset($this->request->post['quantity']))
		{
		$quantity = (int)$this->request->post['quantity'];
			
		} 
		else 
		{
			$quantity = 1;
		}

		if (isset($this->request->post['customerid']))
		{
		$customerid = (int)$this->request->post['customerid'];
			
		} 
		else 
		{
			$customerid = 0;
		}


		$this->db->query("INSERT INTO " . DB_PREFIX . "cart SET   customer_id = '". (int)$customerid ."', session_id = '" . $this->db->escape($this->session->getId()) . "', product_id = '" . (int)$product_id . "', store_id = '" . (int)14 . "',  quantity = '" . (int)$quantity . "', date_added = NOW()");
		
		
		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

	//	echo 	$product_info ['quantity'];
		//var_dump($product_info);

	if ($product_info) {

			
		if (!$json) {

		
	 $json['success'] = "success product added   ";

    $json['producttotal']=$quantity;

	//var_dump($json['producttotal']);

	$json['productID']=$this->request->post['product_id'];

	$json['minimum']=$product_info['minimum'];    
	
					//  var_dump( $count)   
			
			// $count2=$query->row['total'];

		   $json['productQuantity']  = $quantity;		   

			// 	// Unset all shipping and payment methods
			// 	unset($this->session->data['shipping_method']);
			// 	unset($this->session->data['shipping_methods']);
			// 	unset($this->session->data['payment_method']);
			// 	unset($this->session->data['payment_methods']);

			// 	// Totals
			// 	$this->load->model('setting/extension');

			// 	$totals = array();
			// 	$taxes = $this->cart->getTaxes();
			// 	$total = 0;
		
				// Because __call can not keep var references so we put them into an array. 			
				// $total_data = array(
				// 	'totals' => &$totals,
				// 	'taxes'  => &$taxes,
				// 	'total'  => &$total
				// );

				// // Display prices
				// if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				// 	$sort_order = array();

				// 	$results = $this->model_setting_extension->getExtensions('total');

				// 	foreach ($results as $key => $value) {
				// 		$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				// 	}

				// 	array_multisort($sort_order, SORT_ASC, $results);

				// 	foreach ($results as $result) {
				// 		if ($this->config->get('total_' . $result['code'] . '_status')) {
				// 			$this->load->model('extension/total/' . $result['code']);

				// 			// We have to put the totals in an array so that they pass by reference.
				// 			$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				// 		}
				// 	}

				// 	$sort_order = array();

				// 	foreach ($totals as $key => $value) {
				// 		$sort_order[$key] = $value['sort_order'];
				// 	}

				// 	array_multisort($sort_order, SORT_ASC, $totals);
				// }

				// $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
		   
		        // }
				//  else 
				//  {
				// $json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']));
			    //  }
		
			}

    	}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	////  newly added function for update product quantity on decrementing the textbox value 
		public function update() 
		{
			$this->load->language('checkout/cart');

			$json = array();

			if (isset($this->request->post['product_id'])) 
			{
				$product_id = (int)$this->request->post['product_id'];
			} 
			
			else 
			{
			$product_id = 0;
			}

			if (isset($this->request->post['customerid']))
		{
		$customerid = (int)$this->request->post['customerid'];
			
		} 

				$this->load->model('catalog/product');

				$product_info = $this->model_catalog_product->getProduct($product_id);

	// var_dump($product_info);

		if ($product_info) {

				if (isset($this->request->post['quantity']))
				{
				$quantity = (int)$this->request->post['quantity'];
					
				} 
				else 
				{
						$quantity = 1;
				}
				
		if (!$json) {
			//$this->cart->update($this->request->post['product_id'], $quantity);


			$this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = '" . (int)$quantity . "' WHERE  product_id = '" . (int)$product_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	

			//$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

			$json['success'] ="success on update";

			$json['minimum']=$product_info['minimum'];    

			$json['maximumquantity']=$product_info['quantity'];

			$json['price']=$product_info['price'];

		
			// $json['total']= $product_total;

		// Totals
			// $this->load->model('setting/extension');

			// $totals = array();
			// $taxes = $this->cart->getTaxes();
			// $total = 0;

	    // Because __call can not keep var references so we put them into an array. 
				
			// $total_data = array(
			// 	'totals' => &$totals,
			// 	'taxes'  => &$taxes,
			// 	'total'  => &$total
			// );

	// Display prices
			// if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			// 	$sort_order = array();

			// 	$results = $this->model_setting_extension->getExtensions('total');

			// 	foreach ($results as $key => $value) {
			// 		$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			// 	}

			// 	array_multisort($sort_order, SORT_ASC, $results);

			// 	foreach ($results as $result) {
			// 		if ($this->config->get('total_' . $result['code'] . '_status')) {
			// 			$this->load->model('extension/total/' . $result['code']);

			// 			// We have to put the totals in an array so that they pass by reference.
			// 			$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
			// 		}
			// 	}

			// 	$sort_order = array();

			// 	foreach ($totals as $key => $value) {
			// 		$sort_order[$key] = $value['sort_order'];
			// 	}

			// 	array_multisort($sort_order, SORT_ASC, $totals);
			// }

   //  $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts()   , $this->currency->format($total, $this->session->data['currency']));
	
   
	  }

	   }

	   $this->response->addHeader('Content-Type: application/json');
	   $this->response->setOutput(json_encode($json));


   }



		public function edit() {
			$this->load->language('checkout/cart');

			$json = array();

		// Update
		if (!empty($this->request->post['quantity'])) {
			foreach ($this->request->post['quantity'] as $key => $value) {
				$this->cart->update($key, $value);
			}

		
		var_dump($key);

		$this->session->data['success'] = $this->language->get('text_remove');

		unset($this->session->data['shipping_method']);
		unset($this->session->data['shipping_methods']);
		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['reward']);

		$this->response->redirect($this->url->link('checkout/cart'));
	}

	$this->response->addHeader('Content-Type: application/json');
	$this->response->setOutput(json_encode($json));
}

         

	public function remove() {
		
		$this->load->language('checkout/cart');

		$json = array();

		// Remove
		if (isset($this->request->post['key'])) {

     	$key = $this->request->post['key'];

		if (!$json) {

		$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE product_id = '" . (int)$key . "'   AND session_id = '" . $this->db->escape($this->session->getId()) . "' ");
	
		//	$this->cart->remove($this->request->post['key']);

			//unset($this->session->data['vouchers'][$this->request->post['key']]);

			//$json['success'] = $this->language->get('text_remove');

			$json['success'] = "productremoved";

		  //    unset($this->session->data['shipping_method']);
		// 	unset($this->session->data['shipping_methods']);
		// 	unset($this->session->data['payment_method']);
		// 	unset($this->session->data['payment_methods']);
		// 	unset($this->session->data['reward']);

			// Totals
		//	$this->load->model('setting/extension');

			// $totals = array();
			// $taxes = $this->cart->getTaxes();
			// $total = 0;

			// Because __call can not keep var references so we put them into an array. 			
			// $total_data = array(
			// 	'totals' => &$totals,
			// 	'taxes'  => &$taxes,
			// 	'total'  => &$total
			// );

			// Display prices
			// if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			// 	$sort_order = array();

			// 	$results = $this->model_setting_extension->getExtensions('total');

			// 	foreach ($results as $key => $value) {
			// 		$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			// 	}

				//array_multisort($sort_order, SORT_ASC, $results);

				// foreach ($results as $result) {
				// 	if ($this->config->get('total_' . $result['code'] . '_status')) {
				// 		$this->load->model('extension/total/' . $result['code']);

				// 		// We have to put the totals in an array so that they pass by reference.
				// 		$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				// 	}
				// }

				//$sort_order = array();

				// foreach ($totals as $key => $value) {
				// 	$sort_order[$key] = $value['sort_order'];
				// }
 
				// array_multisort($sort_order, SORT_ASC,$totals);
			}

		}

			//$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
		

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	}

