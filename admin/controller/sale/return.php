<?php

require_once __DIR__.'/../../../system/library/razorpay-sdk/Razorpay.php';
use Razorpay\Api\Api;

class ControllerSaleReturn extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/return');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/return');

		// if (isset($this->request->post['payment_id'])) {

		// 	$api = new Api('rzp_test_R3N5AIjsQ9GrBK', 'RQIEs4w068gajIRxCHnedGSe');
		// 	$payment = $api->payment->fetch('pay_Hn7zN8lkLg5VuP');

		// 	if (empty($this->request->post['refund_amount'])){
				
		// 		$refund = $payment->refund(array('amount' => 1000)); 
		// 	}
		// 	else{
    
	    //         $refund_amount = (int)$this->request->post['refund_amount']*100;
        //         $refund = $payment->refund(array('amount' => $refund_amount)); 
	           

		// 	}
			
		// 	if($refund['id'] != ""){
		// 		$success_msg = "Refund Successfull";
		// 		$this->getList($success_msg);
		// 	}
		// 	else{
		// 		$success_msg = "Refund UnSuccessfull";
		// 		$this->getList($success_msg);
		// 	}
		// } else
		// {

			if (isset($this->request->get['refund'])) {
				$this->getList($this->request->get['refund']);
			}
			else{
				$this->getList();
			}
		
		//}
	}
	
	
	public function refund(){

		$this->load->model('sale/return');
		$this->load->language('sale/return');
		$this->document->setTitle($this->language->get('heading_title'));

		$keys = array_keys($this->request->post);
		foreach($keys as $id){

	       $order_id = $this->request->post[$id];
	
         }

       $totalRefundAmount = 0;
   
       $amounts =  $this->model_sale_return->getTotalReturnRefundAmount($order_id);
   
		   if(count($amounts) > 0){
			   foreach ($amounts as $amount) {

				 $totalRefundAmount = (int)$totalRefundAmount + (int)$amount['total'] + (int)$amount['tax'];
			   
			   }
		   }

        $amounts =  $this->model_sale_return->getTotalCancelRefundAmount($order_id);
   
		   if(count($amounts) > 0){
			   foreach ($amounts as $amount) {

				 $totalRefundAmount = (int)$totalRefundAmount + (int)$amount['total'] + (int)$amount['tax'];
			   
			   }
		   }	
		   
		$amounts =  $this->model_sale_return->getTotalItemNotReceivedRefundAmount($order_id);
   
		   if(count($amounts) > 0){
			   foreach ($amounts as $amount) {

				 $totalRefundAmount = (int)$totalRefundAmount + (int)$amount['total'] + (int)$amount['tax'];
			   
			   }
		   }	


        $payment_id = $this->model_sale_return->getPaymentIdByOderId($order_id); 

		if (trim($payment_id)) {

           // var_dump("true");
		   $this->load->model('user/user');
		   $this->load->model('storecontract/storecontract');

		   
           $user_info = $this->model_user_user->getUser($this->user->getId());
           $store_id = $user_info['store_id'];
		   $store_contract_info = $this->model_storecontract_storecontract->getStorecontract( $store_id );
			
		   $api = new Api($store_contract_info['razorpay_key_id'], $store_contract_info['razorpay_key_secret']);
			$payment = $api->payment->fetch($payment_id);
    
	            $refund_amount = (int)$totalRefundAmount;
				
                $refund = $payment->refund(array('amount' => $refund_amount)); 
			if($refund['id'] != ""){

				$this->model_sale_return->setRefundSuccess($order_id);
				$success_msg = "Refund Successful";
				$this->response->redirect($this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&refund=' .$success_msg  , true));
				//$this->getList($success_msg);
			}
			else{
				$success_msg = "Refund UnSuccessfull";
				$this->getList($success_msg);
			}
		}
		 else
		{
			
                        
				$success_msg = "Refund Unsuccessful - Payment Id Not Captured ";
				$this->response->redirect($this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&refund=' .$success_msg  , true));
		
		}


	}

	public function codRefund(){

		$this->load->model('sale/return');
		$this->load->language('sale/return');
		$this->document->setTitle($this->language->get('heading_title'));

		$keys = array_keys($this->request->post);
		foreach($keys as $id){

	       $order_id = $this->request->post[$id];
	
         }
		 $this->model_sale_return->setRefundSuccess($order_id);
		 $success_msg = "Refund Successful";
		 $this->response->redirect($this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&refund=' .$success_msg  , true));
	}


	public function add() {
		$this->load->language('sale/return');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/return');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

		//	var_dump($this->request->post);
		//return;
			$keys = array_keys($this->request->post);
			$product_ids = [];
			$order_ids = [];

			foreach($keys as $id){
				
				if (strpos($id, 'Replacement') !== false) {
					
					$product_order_id = explode("Replacement",$id);
					$this->model_sale_return->addReturnProduct($product_order_id[0], $product_order_id[1], $this->request->post[$id]);
				
				}
				elseif (strpos($id, 'Cancel') !== false) {

					$product_order_id = explode("Cancel",$id);
					$this->model_sale_return->addCancelProduct($product_order_id[0], $product_order_id[1], $this->request->post[$id]);
				
				}
				elseif (strpos($id, 'Delivery') !== false) {

					$product_order_id = explode("Delivery",$id);
					$this->model_sale_return->addItemNotReceivedProduct($product_order_id[0], $product_order_id[1], $this->request->post[$id]);
				
				}
			}			

			//$this->model_sale_return->addReturn($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_return_id'])) {
				$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
			}

			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
	

	public function edit() {
		$this->load->language('sale/return');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/return');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_return->editReturn($this->request->get['return_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_return_id'])) {
				$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
			}

			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
	
//delete
	public function delete() {
		$this->load->language('sale/return');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/return');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $return_id) {
				$this->model_sale_return->deleteReturn($return_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_return_id'])) {
				$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
			}

			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList($datas = "") {

		

		if (isset($this->request->get['filter_return_id'])) {
			$filter_return_id = $this->request->get['filter_return_id'];
		} else {
			$filter_return_id = '';
		}

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = '';
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = '';
		}

		if (isset($this->request->get['filter_product'])) {
			$filter_product = $this->request->get['filter_product'];
		} else {
			$filter_product = '';
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = '';
		}

		if (isset($this->request->get['filter_return_status_id'])) {
			$filter_return_status_id = $this->request->get['filter_return_status_id'];
		} else {
			$filter_return_status_id = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.return_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_return_id'])) {
			$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('sale/return/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('sale/return/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['returns'] = array();

		$filter_data = array(
			'filter_order_id'         => $filter_order_id,
			'filter_customer'         => $filter_customer,
			'filter_product'          => $filter_product,
			'filter_model'            => $filter_model,
			'filter_return_status_id' => $filter_return_status_id,
			'filter_date_added'       => $filter_date_added,
			'filter_date_modified'    => $filter_date_modified,
			'sort'                    => $sort,
			'order'                   => $order,
			'start'                   => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                   => $this->config->get('config_limit_admin')
		);

		//$return_total = $this->model_sale_return->getTotalReturns($filter_data);

		$returnsData = $this->model_sale_return->getReturns($filter_data);
		$itemNotReceivedData =  $this->model_sale_return->getItemNotReceived($filter_data);
		$cancelData =  $this->model_sale_return->getCancel($filter_data);
        
		$results = array_merge($returnsData, $itemNotReceivedData, $cancelData);

		$this->load->model('user/user');
 
		$user_info = $this->model_user_user->getUser($this->user->getId());
 
		$store_id = $user_info['store_id'];
		
		if (isset($this->request->get['filter_return_status_id'])) {

			if($this->request->get['filter_return_status_id'] == "open"){
			$newArray = array_filter($results, function ($var) use($store_id) {
				return ($var['store_id']  == $store_id and ($var['return_status_id'] == '2' or $var['return_status_id'] == '8' or $var['return_status_id'] == '3'  or $var['return_status_id'] == '10'  or $var['return_status_id'] == '4' or $var['return_status_id'] == '9'));
			});
		}
		else{

			$newArray = array_filter($results, function ($var) use($store_id) {
				return ($var['store_id']  == $store_id and ($var['return_status_id'] == '7'));
			});

		}

		}
        else{

		$newArray = array_filter($results, function ($var) use($store_id) {
		  
			return ($var['store_id']  == $store_id and ($var['return_status_id'] == '2' or $var['return_status_id'] == '8' or $var['return_status_id'] == '3'  or $var['return_status_id'] == '10'  or $var['return_status_id'] == '4' or $var['return_status_id'] == '9' or $var['return_status_id'] == '7'));
		});
	}

        $output = array_intersect_key(
			$newArray, 
			array_unique(array_map(function($item) {
				return $item['order_id'];
			}, $newArray))
		);

		
		$return_total = count($output);

		foreach ($output as $result) {
             
			$items_to_refund_count = array_filter($newArray, function ($var) use ($result){
				return ( $var['order_id'] == $result['order_id'] and $var['request_type'] == 'Refund' );
			});

			$items_to_deliver_count = array_filter($newArray, function ($var) use ($result){
				return ( $var['order_id'] == $result['order_id'] and ( $var['request_type'] == 'Replacement' or $var['request_type'] == 'Delivery' ));
			});

			$items_to_cancel_count = array_filter($newArray, function ($var) use ($result){
				return ( $var['order_id'] == $result['order_id'] and $var['request_type'] == 'Cancel');
			});

			
            $totalRefundAmount = 0;
			
			$amounts =  $this->model_sale_return->getTotalReturnRefundAmount($result['order_id']);
			
			        if(count($amounts) > 0){
						foreach ($amounts as $amount) {

                          $totalRefundAmount = (int)$totalRefundAmount + (int)$amount['total'] + (int)$amount['tax'];
						
						}
					}

			$amounts =  $this->model_sale_return->getTotalCancelRefundAmount($result['order_id']);
			
			        if(count($amounts) > 0){
						foreach ($amounts as $amount) {

                          $totalRefundAmount = (int)$totalRefundAmount + (int)$amount['total'] + (int)$amount['tax'];
						
						}
					}

					$amounts =  $this->model_sale_return->getTotalItemNotReceivedRefundAmount($result['order_id']);
			
			        if(count($amounts) > 0){
						foreach ($amounts as $amount) {

                          $totalRefundAmount = (int)$totalRefundAmount + (int)$amount['total'] + (int)$amount['tax'];
						
						}
					}
					
					

			//var_dump((int)$totalRefundAmount);
            
			$data['returns'][] = array(
				'order_id'      => $result['order_id'],
				'customer'      => $result['customer'],
				'product'       => $result['product'],
				'model'         => $result['model'],
				'return_status_id' => $result['return_status_id'],
				'payment_mode'  => $result['payment_mode'],
				'items_to_deliver_count' => count($items_to_deliver_count),
				'items_to_refund_count' => count($items_to_refund_count),
				'items_to_cancel_count' => count($items_to_cancel_count),
				'total_refund_amount' => $totalRefundAmount,
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'edit'          => $this->url->link('sale/return/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}


		



		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}


		if ( $datas!= "") {
			$data['success'] = $datas;

		    $datas = "";
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_return_id'])) {
			$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_return_id'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&sort=r.return_id' . $url, true);
		$data['sort_order_id'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&sort=r.order_id' . $url, true);
		$data['sort_customer'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_product'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&sort=r.product' . $url, true);
		$data['sort_model'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&sort=r.model' . $url, true);
		$data['sort_status'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_date_added'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&sort=r.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&sort=r.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $return_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit');
		$pagination->url = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($return_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($return_total - $this->config->get('config_limit_admin'))) ? $return_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $return_total, ceil($return_total / $this->config->get('config_limit_admin')));

		$data['filter_return_id'] = $filter_return_id;
		$data['filter_order_id'] = $filter_order_id;
		$data['filter_customer'] = $filter_customer;
		$data['filter_product'] = $filter_product;
		$data['filter_model'] = $filter_model;
		$data['filter_return_status_id'] = $filter_return_status_id;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$this->load->model('localisation/return_status');

		$data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();
		 
		$data['refundAction'] = $this->url->link('sale/return/refund', 'user_token=' . $this->session->data['user_token'], true);
		$data['codRefundAction'] = $this->url->link('sale/return/codRefund', 'user_token=' . $this->session->data['user_token'], true);

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/return_list', $data));
	}

	protected function getForm() {

		$data['text_form'] = !isset($this->request->get['return_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['return_id'])) {
			$data['return_id'] = $this->request->get['return_id'];
		} else {
			$data['return_id'] = 0;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['order_id'])) {
			$data['error_order_id'] = $this->error['order_id'];
		} else {
			$data['error_order_id'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = '';
		}

		if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_return_id'])) {
			$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['return_id'])) {
			//$data['action'] = $this->url->link('sale/return/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			//$data['action'] = $this->url->link('sale/return/edit', 'user_token=' . $this->session->data['user_token'] . '&return_id=' . $this->request->get['return_id'] . $url, true);
		}
		$data['cancel'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['return_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$return_info = $this->model_sale_return->getReturn($this->request->get['return_id']);
			$return_payment_id = $this->model_sale_return->getPaymentIdByOderId($return_info['order_id']);
			$return_total_amount = $this->model_sale_return->getgrandTotalByOderId($return_info['order_id']);
			
		//	var_dump($return_payment_id['payment_id']);
		//	var_dump($return_total_amount['total']);
		}
		$data['actionRefund'] = $this->url->link('sale/return/refund', 'user_token=' . $this->session->data['user_token'] ,true);

		if (isset($this->request->post['order_id'])) {
			$data['order_id'] = $this->request->post['order_id'];
		} elseif (!empty($return_info)) {
			$data['order_id'] = $return_info['order_id'];
		} else {
			$data['order_id'] = '';
		}

		if (isset($this->request->post['date_ordered'])) {
			$data['date_ordered'] = $this->request->post['date_ordered'];
		} elseif (!empty($return_info)) {
			$data['date_ordered'] = ($return_info['date_ordered'] != '0000-00-00' ? $return_info['date_ordered'] : '');
		} else {
			$data['date_ordered'] = '';
		}

		if (isset($this->request->post['customer'])) {
			$data['customer'] = $this->request->post['customer'];
		} elseif (!empty($return_info)) {
			$data['customer'] = $return_info['customer'];
		} else {
			$data['customer'] = '';
		}

		if (isset($this->request->post['customer_id'])) {
			$data['customer_id'] = $this->request->post['customer_id'];
		} elseif (!empty($return_info)) {
			$data['customer_id'] = $return_info['customer_id'];
		} else {
			$data['customer_id'] = '';
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($return_info)) {
			$data['firstname'] = $return_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($return_info)) {
			$data['lastname'] = $return_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($return_info)) {
			$data['email'] = $return_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($return_info)) {
			$data['telephone'] = $return_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['product'])) {
			$data['product'] = $this->request->post['product'];
		} elseif (!empty($return_info)) {
			$data['product'] = $return_info['product'];
		} else {
			$data['product'] = '';
		}

		if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
		} elseif (!empty($return_info)) {
			$data['product_id'] = $return_info['product_id'];
		} else {
			$data['product_id'] = '';
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($return_info)) {
			$data['model'] = $return_info['model'];
		} else {
			$data['model'] = '';
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($return_info)) {
			$data['quantity'] = $return_info['quantity'];
		} else {
			$data['quantity'] = '';
		}

		if (isset($this->request->post['opened'])) {
			$data['opened'] = $this->request->post['opened'];
		} elseif (!empty($return_info)) {
			$data['opened'] = $return_info['opened'];
		} else {
			$data['opened'] = '';
		}

		if (isset($this->request->post['return_reason_id'])) {
			$data['return_reason_id'] = $this->request->post['return_reason_id'];
		} elseif (!empty($return_info)) {
			$data['return_reason_id'] = $return_info['return_reason_id'];
		} else {
			$data['return_reason_id'] = '';
		}

		$this->load->model('localisation/return_reason');

		$data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();

		if (isset($this->request->post['return_action_id'])) {
			$data['return_action_id'] = $this->request->post['return_action_id'];
		} elseif (!empty($return_info)) {
			$data['return_action_id'] = $return_info['return_action_id'];
		} else {
			$data['return_action_id'] = '';
		}

		$this->load->model('localisation/return_action');

		$data['return_actions'] = $this->model_localisation_return_action->getReturnActions();

		if (isset($this->request->post['comment'])) {
			$data['comment'] = $this->request->post['comment'];
		} elseif (!empty($return_info)) {
			$data['comment'] = $return_info['comment'];
		} else {
			$data['comment'] = '';
		}

		if (isset($this->request->post['return_status_id'])) {
			$data['return_status_id'] = $this->request->post['return_status_id'];
		} elseif (!empty($return_info)) {
			$data['return_status_id'] = $return_info['return_status_id'];
		} else {
			$data['return_status_id'] = '';
		}




		if (isset($this->request->post['payment_id'])) {
			$data['payment_id'] = $this->request->post['payment_id'];
		} elseif (!empty($return_info)) {
			$data['payment_id'] = $return_payment_id['payment_id'];
		} else {
			$data['payment_id'] = '';
		}

		if (isset($this->request->post['total_amount'])) {
			$data['total_amount'] = $this->request->post['total_amount'];
		} elseif (!empty($return_info)) {
			$data['total_amount'] = $return_total_amount['total'];
		} else {
			$data['total_amount'] = '';
		}





            $data['order_id'] = $this->request->get['order_id'];
		    $data['products'] = array();

			$OpenReturns = $this->model_sale_return->getOpenReturns($this->request->get['order_id']);
			$OpenCancel = $this->model_sale_return->getOpenCancel($this->request->get['order_id']);
			$OpenItemNotReceived = $this->model_sale_return->getOpenItemNotReceived($this->request->get['order_id']);
			
			$products = array_merge($OpenReturns, $OpenCancel, $OpenItemNotReceived);
			//var_dump($products);
			$newArray = array_filter($products, function ($var) {
				return ($var['request_type'] == 'Replacement' or $var['request_type'] == 'Delivery' or $var['request_type'] == 'Cancel' );
			});

			if( count($newArray) > 0){
				$data['submitButton'] = "Yes";
			}
			else{
				$data['submitButton'] = "No";
			}

			foreach ($products as $product) {
				
				$data['products'][] = array(
			        'order_id' => $product['order_id'],
					'product_id' => $product['product_id'],
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

	    $data['action'] = $this->url->link('sale/return/add', 'user_token=' . $this->session->data['user_token'], true);

		$this->load->model('localisation/return_status');

		$data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();
       
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/return_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'sale/return')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['order_id'])) {
			$this->error['order_id'] = $this->language->get('error_order_id');
		}

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if ((utf8_strlen($this->request->post['product']) < 1) || (utf8_strlen($this->request->post['product']) > 255)) {
			$this->error['product'] = $this->language->get('error_product');
		}

		if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
			$this->error['model'] = $this->language->get('error_model');
		}

		if (empty($this->request->post['return_reason_id'])) {
			$this->error['reason'] = $this->language->get('error_reason');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/return')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function history() {
		$this->load->language('sale/return');

		$this->load->model('sale/return');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$results = $this->model_sale_return->getReturnHistories($this->request->get['return_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_sale_return->getTotalReturnHistories($this->request->get['return_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('sale/return/history', 'user_token=' . $this->session->data['user_token'] . '&return_id=' . $this->request->get['return_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('sale/return_history', $data));
	}
	
	public function addHistory() {
		$this->load->language('sale/return');

		$json = array();

		if (!$this->user->hasPermission('modify', 'sale/return')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('sale/return');

			$this->model_sale_return->addReturnHistory($this->request->get['return_id'], $this->request->post['return_status_id'], $this->request->post['comment'], $this->request->post['notify']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}	
}