<?php
class ControllerExtensionDashboardSale extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/dashboard/sale');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_sale', $this->request->post);

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
			'href' => $this->url->link('extension/dashboard/sale', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/dashboard/sale', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true);

		if (isset($this->request->post['dashboard_sale_width'])) {
			$data['dashboard_sale_width'] = $this->request->post['dashboard_sale_width'];
		} else {
			$data['dashboard_sale_width'] = $this->config->get('dashboard_sale_width');
		}
	
		$data['columns'] = array();
		
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}
				
		if (isset($this->request->post['dashboard_sale_status'])) {
			$data['dashboard_sale_status'] = $this->request->post['dashboard_sale_status'];
		} else {
			$data['dashboard_sale_status'] = $this->config->get('dashboard_sale_status');
		}

		if (isset($this->request->post['dashboard_sale_sort_order'])) {
			$data['dashboard_sale_sort_order'] = $this->request->post['dashboard_sale_sort_order'];
		} else {
			$data['dashboard_sale_sort_order'] = $this->config->get('dashboard_sale_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/sale_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/dashboard/sale')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function dashboard() {
		$this->load->language('extension/dashboard/sale');

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('extension/dashboard/sale');

		$today = $this->model_extension_dashboard_sale->getTotalSales(array('filter_date_added' => date('Y-m-d', strtotime('-1 day'))));

		$yesterday = $this->model_extension_dashboard_sale->getTotalSales(array('filter_date_added' => date('Y-m-d', strtotime('-2 day'))));

		$difference = $today - $yesterday;

		if ($difference && (int)$today) {
			$data['percentage'] = round(($difference / $today) * 100);
		} else {
			$data['percentage'] = 0;
		}

      $this->load->model('user/user');
		$this->load->model('setting/store');

		$customer_group = $this->model_customer_customer->getCustomer($this->customer->getId());

	       //$cid=$customer_group['store_id'];
        //echo $cid;

		$user_info = $this->model_user_user->getUser($this->user->getId());

		if ($user_info) 
		{
			$data['userid']= $user_info['user_id'];
			
			$data['storeid']= $user_info['store_id'];
		
			$data['user_group'] = $user_info['user_group_id'];

             if($data['user_group'] ==1)
             {
                $sale_total = $this->model_extension_dashboard_sale->getTotalSales();
          //  var_dump( $sale_total);
		if ($sale_total > 1000000000000) {
			$data['total'] = round($sale_total / 1000000000000, 1) . 'T';
		} elseif ($sale_total > 1000000000) {
			$data['total'] = round($sale_total / 1000000000, 1) . 'B';
		} elseif ($sale_total > 1000000) {
			$data['total'] = round($sale_total / 1000000, 1) . 'M';
		} elseif ($sale_total > 1000) {
			$data['total'] = round($sale_total / 1000, 1) . 'K';
		} else {
			$data['total'] = round($sale_total);
		}
          
	 }	

	 else
	 {
	   if( $data['user_group'] !=1 )
		{
			$results = $this->model_setting_store->getStores();
			foreach ($results as $result) 
			{
			  $storeid=$result['store_id'];
			//var_dump($storeid);
	       if($data['storeid']==$storeid)
			{
			$sale_total = $this->model_extension_dashboard_sale->getTotalSalesofstore($storeid);
		//var_dump($sale_total);
			}
			}
		
			if ($sale_total > 1000000000000) {
				$data['total'] = round($sale_total / 1000000000000, 1) . 'T';
			} elseif ($sale_total > 1000000000) {
				$data['total'] = round($sale_total / 1000000000, 1) . 'B';
			} elseif ($sale_total > 1000000) {
				$data['total'] = round($sale_total / 1000000, 1) . 'M';
			} elseif ($sale_total > 1000) {
				$data['total'] = round($sale_total / 1000, 1) . 'K';
			} else {
				$data['total'] = round($sale_total);
			}
		 }
			}
		}


		$data['sale'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true);

		return $this->load->view('extension/dashboard/sale_info', $data);
	}
}
