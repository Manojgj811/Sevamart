<?php
class ControllerExtensionReportProductPurchased extends Controller {
	public function index() {
		$this->load->language('extension/report/product_purchased');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('report_product_purchased', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/report/product_purchased', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/report/product_purchased', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true);

		if (isset($this->request->post['report_product_purchased_status'])) {
			$data['report_product_purchased_status'] = $this->request->post['report_product_purchased_status'];
		} else {
			$data['report_product_purchased_status'] = $this->config->get('report_product_purchased_status');
		}

		if (isset($this->request->post['report_product_purchased_sort_order'])) {
			$data['report_product_purchased_sort_order'] = $this->request->post['report_product_purchased_sort_order'];
		} else {
			$data['report_product_purchased_sort_order'] = $this->config->get('report_product_purchased_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/report/product_purchased_form', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/report/product_purchased')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
		
	public function report() {
		$this->load->language('extension/report/product_purchased');

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = 0;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$this->load->model('extension/report/product');

		$data['products'] = array();

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$product_total = $this->model_extension_report_product->getTotalPurchased($filter_data);


		$this->load->model('user/user');
				
		$user_info = $this->model_user_user->getUser($this->user->getId());
////changes done on 18-04-2021  for admin 
	
	//	var_dump($user_info);

          if ($user_info) 
			{
				$data['user_group'] = $user_info['user_group_id'];

			$this->load->model('setting/store');

				$this->load->model('setting/setting');

				$storeresults = $this->model_setting_store->getStores();

				if($data['user_group']==1)
				{
					foreach($storeresults as $result) 
					{
					$data['stores'][] = array(
					'name' => $result['name'],
					'storeid'=>$result['store_id']
						);
				    }
			

		$results = $this->model_extension_report_product->getPurchased($filter_data);
         //  var_dump($results);

		foreach ($results as $result) {
			$data['products'][] = array(
				'name'     => $result['name'],
				'model'    => $result['model'],
				'store' => $result['store_id'],
				'quantity'   =>$result['quantity'],
				'total'    => $this->currency->format($result['total'], $this->config->get('config_currency'))
			);
	        	}
			}
		}


		if($user_info)
		{
		  $data['storeid']= $user_info['store_id'];
			  
		  $data['user_group'] = $user_info['user_group_id'];
  
					  if($data['user_group']!=1)
					  {
					$this->load->model('setting/store');
				
					  $storeresults2 = $this->model_setting_store->getStores();
					  
				  foreach ($storeresults2 as $storeresult) 
				  {
					 $storeid=$storeresult['store_id'];
				  
				   if($data['storeid']==$storeid)
				  {
					 $results2=$this->model_extension_report_product->getPurchasedbasedonstore($filter_data,$storeid);
					// var_dump($results2);
				  }
				  }
  
				  foreach ($results2 as $result) {
					$data['products'][] = array(
						'name'     => $result['name'],
						'model'    => $result['model'],
						'store' => $result['store_name'],
						'quantity'   =>$result['quantity'],
						'total'    => $this->currency->format($result['total'], $this->config->get('config_currency'))
					);
						}
		  }
	  }



		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=product_purchased' . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_order_status_id'] = $filter_order_status_id;

		return $this->load->view('extension/report/product_purchased_info', $data);
	}
}