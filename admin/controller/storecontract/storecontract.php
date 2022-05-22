
<?php
class ControllerStorecontractStorecontract extends Controller 
{
	private $error = array();

	public function index() {
		$this->load->language('storecontract/storecontract');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('storecontract/storecontract');

		$this->getList();
	}


	public function add() {
		$this->load->language('storecontract/storecontract');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->load->model('storecontract/storecontract');

			$this->model_storecontract_storecontract->addStorecontract($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('storecontract/storecontract', 'user_token=' . $this->session->data['user_token'], true));
		
		}
		

		$this->getForm();
	}

	public function edit() {
		$this->load->language('storecontract/storecontract');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('storecontract/storecontract');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_storecontract_storecontract->editStorecontract($this->request->get['store_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('storecontract/storecontract', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true));
		}

		$this->getForm();
	}


	public function delete() {
		$this->load->language('storecontract/storecontract');

		$this->document->setTitle($this->language->get('heading_title'));
	
		$this->load->model('storecontract/storecontract');
		
		if (isset($this->request->post['selected'])) {
		
			$this->load->model('storecontract/storecontract');
			 foreach ($this->request->post['selected'] as $store_contract_id) {

				$this->model_storecontract_storecontract->deleteStorecontract($store_contract_id);
			}
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('storecontract/storecontract', 'user_token=' . $this->session->data['user_token'], true));
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
			'href' => $this->url->link('storecontract/storecontract', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('storecontract/storecontract/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('storecontract/storecontract/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['storecontracts'] = array();
		//$storesubscription_total = $this->model_storesubscription_storesubscription->getTotalStoreSubscription();
			
		$results = $this->model_storecontract_storecontract->getStorescontracts();


		foreach ($results as $result) {
			$data['storecontracts'][] = array(
				'store_contract_id' => $result['store_contract_id'],
				'store_id' => $result['Storename'],	
				'commission_percentage_card'  => $result['commission_percentage_card'],
				'commission_percentage_cod'  => $result['commission_percentage_cod'],
				'tax_class_id'  => $result['Gsttitle'],
				'GSTIN'  => $result['GSTIN'],
				'razorpay_key_id' => $result['razorpay_key_id'],
				'razorpay_key_secret' => $result['razorpay_key_secret'],	
				'edit'  => $this->url->link('storecontract/storecontract/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $result['store_id'], true)
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

		$this->response->setOutput($this->load->view('storecontract/storecontract_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['store_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		
		if (isset($this->error['store'])) {
			$data['error_store'] = $this->error['store'];
		} else {
			$data['error_store'] = '';
		}

		if (isset($this->error['card'])) {
			$data['error_card'] = $this->error['card'];
		} else {
			$data['error_card'] = '';
		}


		if (isset($this->error['cod'])) {
			$data['error_cod'] = $this->error['cod'];
		} else {
			$data['error_cod'] = '';
		}

		// if (isset($this->error['account_id'])) {
		// 	$data['error_account_id'] = $this->error['account_id'];
		// } else {
		// 	$data['error_account_id'] = '';
		// }

		if (isset($this->error['payment_razorpay_key_id'])) {
            $data['error_key_id'] = $this->error['payment_razorpay_key_id'];
        } else {
            $data['error_key_id'] = '';
        }

        if (isset($this->error['payment_razorpay_key_secret'])) {
            $data['error_key_secret'] = $this->error['payment_razorpay_key_secret'];
        } else {
            $data['error_key_secret'] = '';
        }
	
		if (isset($this->error['GSTIN'])) {
			$data['error_GSTIN'] = $this->error['GSTIN'];
		} else {
			$data['error_GSTIN'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('storecontract/storecontract', 'user_token=' . $this->session->data['user_token'], true)
		);
		if (!isset($this->request->get['store_id'])) {
			$data['breadcrumbs'][] = array(
				
				'href' => $this->url->link('storecontract/storecontract/add', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				
				'href' => $this->url->link('storecontract/storecontract/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true)
			);
		}


		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (!isset($this->request->get['store_id'])) {
			$data['action'] = $this->url->link('storecontract/storecontract/add', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('storecontract/storecontract/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);
		}

		$data['cancel'] = $this->url->link('storecontract/storecontract', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST'))
		 {
			 $this->load->model('storecontract/storecontract');

		$store_contract_info = $this->model_storecontract_storecontract->getStorecontract( $this->request->get['store_id']);
		
		}
		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('setting/store');

		$data['stores'] = array();
		

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}
				
		if (isset($this->request->get['store_id'])) {
			$data['store_id'] = $this->request->get['store_id'];
		} elseif (!empty($store_contract_info )) {
			$data['store_id'] = $store_contract_info ['store_id'];
		} else {
			$data['store_id'] = '';
		}		



	if (isset($this->request->post['commission_percentage_card'])) {
		$data['commission_percentage_card'] = $this->request->post['commission_percentage_card'];
	}elseif (isset($store_contract_info ['commission_percentage_card'])) {
		$data['commission_percentage_card'] = $store_contract_info ['commission_percentage_card'];
	} else {
		$data['commission_percentage_card'] = '';
	}
	

	if (isset($this->request->post['commission_percentage_cod'])) {
		$data['commission_percentage_cod'] = $this->request->post['commission_percentage_cod'];
		
	}elseif (isset($store_contract_info ['commission_percentage_cod'])) {
		$data['commission_percentage_cod'] = $store_contract_info ['commission_percentage_cod'];
	} else {
		$data['commission_percentage_cod'] = '';
	}

	// created to post and capture razorpay key id
	if (isset($this->request->post['razorpay_key_id'])) {
		$data['razorpay_key_id'] = $this->request->post['razorpay_key_id'];
	}elseif (isset($store_contract_info ['razorpay_key_id'])) {
		$data['razorpay_key_id'] = $store_contract_info ['razorpay_key_id'];
	} else {
		$data['razorpay_key_id'] = '';
	}


	// created to post and capture razorpay key secret
	if (isset($this->request->post['razorpay_key_id'])) {
		$data['razorpay_key_secret'] = $this->request->post['razorpay_key_secret'];
	}elseif (isset($store_contract_info ['razorpay_key_id'])) {
		$data['razorpay_key_secret'] = $store_contract_info ['razorpay_key_secret'];
	} else {
		$data['razorpay_key_secret'] = '';
	}
	

////changes done on 28-06-2021 for tax rate dropdown by VIDYA 


$this->load->model('localisation/tax_class');

$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();


	if (isset($this->request->get['tax_class_id'])) {
	$data['tax_class_id'] = $this->request->get['tax_class_id'];
	} elseif (!empty($store_contract_info )) {
	$data['tax_class_id'] = $store_contract_info ['tax_class_id'];
	} else {
	$data['tax_class_id'] = 0;
	}

	if (isset($this->request->post['GSTIN'])) {
		$data['GSTIN'] = $this->request->post['GSTIN'];
	}elseif (isset($store_contract_info ['GSTIN'])) {
		$data['GSTIN'] = $store_contract_info ['GSTIN'];
	} else {
		$data['GSTIN'] = '';
	}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('storecontract/storecontract_form', $data));
	}


	protected function validateForm()
	 {
		if (!$this->user->hasPermission('modify', 'storecontract/storecontract')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['store_id']) {
		 	$this->error['store'] = $this->language->get('error_store');
		 }
		
		if (!$this->request->post['commission_percentage_card']) {
			$this->error['card'] = $this->language->get('error_card');
		}
		if (!$this->request->post['commission_percentage_cod']) {
			$this->error['cod'] = $this->language->get('error_cod');
		}

		// if (!$this->request->post['account_id']) {
		// 	$this->error['account_id'] = $this->language->get('error_account_id');
		// }

		  if (!$this->request->post['payment_razorpay_key_id']) {
            $this->error['payment_razorpay_key_id'] = $this->language->get('error_key_id');
        }

        if (!$this->request->post['payment_razorpay_key_secret']) {
            $this->error['payment_razorpay_key_secret'] = $this->language->get('error_key_secret');
        }


	 	 if (utf8_strlen($this->request->post['GSTIN']) < 1 || utf8_strlen($this->request->post['GSTIN']) > 15) {
		$this->error['GSTIN'] = $this->language->get('error_GSTIN');
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	// protected function validateDelete() {
	// 	if (!$this->user->hasPermission('modify', 'storecontract/storecontract')) {
	// 		$this->error['warning'] = $this->language->get('error_permission');
	// 	}

	// 	return !$this->error;
	// }	
	
}



















