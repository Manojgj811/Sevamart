
<?php
class ControllerStoresubscriptionStoresubscription extends Controller 
{
	private $error = array();

	public function index() {
		$this->load->language('storesubscription/storesubscription');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('storesubscription/storesubscription');

		$this->getList();
	}


	public function add() {
		$this->load->language('storesubscription/storesubscription');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('storesubscription/storesubscription');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_storesubscription_storesubscription->addStoreSubscription($this->request->post);
			
			// $this->load->model('storesubscription/storesubscription');

			
			// $this->model_storesubscription_storesubscription->addStoreSubscription('config', $this->request->post, $store_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('storesubscription/storesubscription', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('storesubscription/storesubscription');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('storesubscription/storesubscription');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_storesubscription_storesubscription->editStoreSubscription($this->request->get['store_id'], $this->request->post);

			// $this->load->model('storesubscription/storesubscription');

			// $this->model_storesubscription_storesubscription->editStoreSubscription('config', $this->request->post, $this->request->get['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('storesubscription/storesubscription', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true));
		}

		$this->getForm();
	}


	public function delete() {
		$this->load->language('storesubscription/storesubscription');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('storesubscription/storesubscription');

		if (isset($this->request->post['selected'])) {
			$this->load->model('storesubscription/storesubscription');

			
				$this->model_storesubscription_storesubscription->deleteStoreSubscription($data);

				

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('storesubscription/storesubscription', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getList();
	}




	
	protected function getList() {
		$url = '';

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
			'href' => $this->url->link('storesubscription/storesubscription', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('storesubscription/storesubscription/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('storesubscription/storesubscription/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['storesubscriptions'] = array();

		// $data['stores'][] = array(
		// 	'store_id' => 0,
		// 	'name'     => $this->config->get('config_name') . $this->language->get('text_default'),
		// 	'url'      => $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG,
		// 	'edit'     => $this->url->link('storesubscription/storesubscription', 'user_token=' . $this->session->data['user_token'], true)
		// );

		//$storesubscription_total = $this->model_storesubscription_storesubscription->getTotalStoreSubscription();
			
		$results = $this->model_storesubscription_storesubscription->getStoresSubscriptions();
	var_dump($results);
		foreach ($results as $result) {
			$data['storesubscriptions'][] = array(
				'store_subscription_id'     => $result['store_subscription_id'],
				'store'      => $result['store_id'] ? $result['store_id'] : $this->language->get('text_default'),
				//'name'        => $result['name'],
				'commission_percentage_card'  => $result['commission_percentage_card'],
				'commission_percentage_cod'  => $result['commission_percentage_cod'],
				'GST_commission_percentage'  => $result['GST_commission_percentage'],
				'edit'     => $this->url->link('storesubscription/storesubscription/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $result['store_id'], true),
				'delete'      => $this->url->link('storesubscription/storesubscription/delete', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $result['store_id'] . $url, true)
			);
		}

		

		if (isset($this->error['warning'])) {
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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('storesubscription/storesubscription_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['store_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('storesubscription/storesubscription', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['store_id'])) {
			$data['breadcrumbs'][] = array(
				//'text' => $this->language->get('text_settings'),
				'href' => $this->url->link('storesubscription/storesubscription/add', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				//'text' => $this->language->get('text_settings'),
				'href' => $this->url->link('storesubscription/storesubscription/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true)
			);
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

if (!isset($this->request->get['store_id'])) {
			$data['action'] = $this->url->link('storesubscription/storesubscription/add', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('storesubscription/storesubscription/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);
		}

		$data['cancel'] = $this->url->link('storesubscription/storesubscription', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST'))
		 {
			 $this->load->model('storesubscription/storesubscription');

		$store_subscription_info = $this->model_storesubscription_storesubscription->getStoreSubscription($this->request->get['store_id']);
		var_dump($store_subscription_info);
		}
		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		////changes done on 28-06-2021 for store dropdown

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}
				
		if (isset($this->request->get['store_id'])) {
			$data['store_id'] = $this->request->get['store_id'];
		} elseif (!empty($store_subscription_info )) {
			$data['store_id'] = $store_subscription_info ['store_id'];
		} else {
			$data['store_id'] = '';
		}		


	if (isset($this->request->get['store_id'])) {
		$data['commission_percentage_card'] = $this->request->get['commission_percentage_card'];
		var_dump($data['commission_percentage_card']);
		echo"<br>";
	}
	elseif (isset($store_subscription_info ['commission_percentage_card'])) {
		$data['commission_percentage_card'] = $store_subscription_info ['commission_percentage_card'];
	} else {
		$data['commission_percentage_card'] = '';
	}
	

	if (isset($this->request->get['store_id'])) {
		$data['commission_percentage_cod'] = $this->request->post['commission_percentage_cod'];
	
		var_dump( $data['commission_percentage_cod']);
		echo"<br>";
	}
	
	elseif (isset($store_subscription_info ['commission_percentage_cod'])) {
		$data['commission_percentage_cod'] = $store_subscription_info ['commission_percentage_cod'];
	} else {
		$data['commission_percentage_cod'] = '';
	}


////changes done on 28-06-2021 for tax rate dropdown


$this->load->model('localisation/tax_class');

$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

if (isset($this->request->get['store_id'])) {
  $data['tax_class_id'] = $this->request->get['GST_commission_percentage'];
}elseif (!empty($store_subscription_info )) {
  $data['tax_class_id'] = $store_subscription_info ['GST_commission_percentage'];
} else {
  $data['tax_class_id'] = 0;
}



		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('storesubscription/storesubscription_form', $data));
	}
	
	
}



















