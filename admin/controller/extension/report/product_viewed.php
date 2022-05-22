<?php
class ControllerExtensionReportProductViewed extends Controller {
	public function index() {
		$this->load->language('extension/report/product_viewed');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('report_product_viewed', $this->request->post);

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
			'href' => $this->url->link('extension/report/product_viewed', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/report/product_viewed', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true);

		if (isset($this->request->post['report_product_viewed_status'])) {
			$data['report_product_viewed_status'] = $this->request->post['report_product_viewed_status'];
		} else {
			$data['report_product_viewed_status'] = $this->config->get('report_product_viewed_status');
		}

		if (isset($this->request->post['report_product_viewed_sort_order'])) {
			$data['report_product_viewed_sort_order'] = $this->request->post['report_product_viewed_sort_order'];
		} else {
			$data['report_product_viewed_sort_order'] = $this->config->get('report_product_viewed_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/report/product_viewed_form', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/report/product_viewed')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
		
	public function report() {
		$this->load->language('extension/report/product_viewed');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$data['reset'] = $this->url->link('extension/report/product_viewed/reset', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$this->load->model('extension/report/product');

		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$data['products'] = array();

		$product_viewed_total = $this->model_extension_report_product->getTotalProductViews();

		$product_total = $this->model_extension_report_product->getTotalProductsViewed();


		$this->load->model('user/user');
				
		$user_info = $this->model_user_user->getUser($this->user->getId());
////changes done on 26-04-2021  for admin 
	
	//	var_dump($user_info);

          if ($user_info) 
			{
				$data['user_group'] = $user_info['user_group_id'];

			$this->load->model('setting/store');

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
			

		$results = $this->model_extension_report_product->getProductsViewed($filter_data);

		foreach ($results as $result) {
			if ($result['viewed']) {
				$percent = round($result['viewed'] / $product_viewed_total * 100, 2);
			} else {
				$percent = 0;
			}

			$data['products'][] = array(
				'name'    => $result['name'],
				'model'   => $result['model'],
				'viewed'  => $result['viewed'],
				'percent' => $percent . '%'
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
				$results = $this->model_extension_report_product->getProductsViewedbystore($storeid);
                // var_dump($results);
			  }
			  }

			  foreach ($results as $result) {
				if ($result['viewed']) {
					$percent = round($result['viewed'] / $product_viewed_total * 100, 2);
				} else {
					$percent = 0;
				}
	
				$data['products'][] = array(
					'name'    => $result['name'],
					'model'   => $result['model'],
					'viewed'  => $result['viewed'],
					'percent' => $percent . '%'
				);
				}
	  
			}
  
		}




		$data['user_token'] = $this->session->data['user_token'];

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=product_viewed&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
		
		return $this->load->view('extension/report/product_viewed_info', $data);
	}

	public function reset() {
		$this->load->language('extension/report/product_viewed');

		if (!$this->user->hasPermission('modify', 'extension/report/product_viewed')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/report/product');

			$this->model_extension_report_product->reset();

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->response->redirect($this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=product_viewed' . $url, true));
	}
}