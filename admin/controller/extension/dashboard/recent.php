<?php
class ControllerExtensionDashboardRecent extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/dashboard/recent');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_recent', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true));
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
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/dashboard/recent', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/dashboard/recent', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true);

		if (isset($this->request->post['dashboard_recent_width'])) {
			$data['dashboard_recent_width'] = $this->request->post['dashboard_recent_width'];
		} else {
			$data['dashboard_recent_width'] = $this->config->get('dashboard_recent_width');
		}

		$data['columns'] = array();
		
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}
				
		if (isset($this->request->post['dashboard_recent_status'])) {
			$data['dashboard_recent_status'] = $this->request->post['dashboard_recent_status'];
		} else {
			$data['dashboard_recent_status'] = $this->config->get('dashboard_recent_status');
		}

		if (isset($this->request->post['dashboard_recent_sort_order'])) {
			$data['dashboard_recent_sort_order'] = $this->request->post['dashboard_recent_sort_order'];
		} else {
			$data['dashboard_recent_sort_order'] = $this->config->get('dashboard_recent_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/recent_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/dashboard/recent')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function dashboard() {
		$this->load->language('extension/dashboard/recent');

		$data['user_token'] = $this->session->data['user_token'];

		// Last 5 Orders
		$data['orders'] = array();

		$filter_data = array(
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5
		);

    	$this->load->model('user/user');

		$user_info = $this->model_user_user->getUser($this->user->getId());
				
 		$this->load->model('sale/order');
		
		$results = $this->model_sale_order->getOrders($filter_data);


        if($user_info )
		{
		 $data['user_group'] = $user_info['user_group_id'];

			if($data['user_group']==1)
			{ 
				$this->load->model('setting/store');
				$storeresults = $this->model_setting_store->getStores();
			
			$orderresults=$this->model_sale_order->getOrders($filter_data);
			foreach ($orderresults as $result) 
			{
			$storeid=$result['store_id'];
			}
			
		foreach ($orderresults as $result) {
			
			$data['orders'][] = array(
				'order_id'      => $result['order_id'],
				'customer'      => $result['customer'],
				'order_status'  => $result['order_status'] ? $result['order_status'] : $this->language->get('text_missing'),
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'shipping_code' => $result['shipping_code'],
			
				'view'          => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id']  , true),
				'edit'          => $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id']  , true),
				'storeid'        =>$result['store_id']
			);
			}
			
		}
	}
      ///////////////////for store managers recent orders implemented by sharath on 28-05-2021
				if($user_info )
				{
				$data['storeid']= $user_info['store_id'];
				$data['user_group'] = $user_info['user_group_id'];

					if($data['user_group']!=1)
					{
                  $this->load->model('setting/store');
					$storeresults = $this->model_setting_store->getStores();
					
				foreach ($storeresults as $storeresult) 
				{
				$storeid=$storeresult['store_id'];
				
                 if($data['storeid']==$storeid)
				{
              	$results3 = $this->model_sale_order-> getTotalOrdersByStoredetails($filter_data,$storeid);
				}
			  
			    }
			foreach ($results3 as $orderresult) {
				
				$data['orders'][] = array(
					'order_id'      => $orderresult['order_id'],
					'customer'      => $orderresult['firstname'],
					'order_status'=> $orderresult['order_status'] ? $orderresult['order_status'] : $this->language->get('text_missing'),
					'total'         => $this->currency->format($orderresult['total'], $orderresult['currency_code'], $orderresult['currency_value']),
					'date_added'    => date($this->language->get('date_format_short'), strtotime($orderresult['date_added'])),
					'date_modified' => date($this->language->get('date_format_short'), strtotime($orderresult['date_modified'])),
					'shipping_code' => $orderresult['shipping_code'],
					'storeid'      =>$orderresult['store_id'],
					'view'          => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $orderresult['order_id'], true),
					'edit'          => $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $orderresult['order_id'] , true)
				);
				}
				}
			}




		return $this->load->view('extension/dashboard/recent_info', $data);
	}
}
