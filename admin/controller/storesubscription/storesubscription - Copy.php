
<?php
class ControllerStoresubscriptionStoresubscription extends Controller 
{
	
	private $error = array();
	
	public function index() {
			
		$this->load->language('storesubscription/storesubscription');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('storesubscription/storesubscription');			

		$this->load->model('setting/store');
	
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) 
		{
			//$this->model_storesubscription_storesubscription->editStoreSubscription($data)
			//$this->model_storesubscription_storesubscription->deleteStoreSubscription($data)
            $this->model_storesubscription_storesubscription->addStoreSubscription($this->request->post );
		
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('storesubscription/storesubscription', 'user_token=' . $this->session->data['user_token'] . $url, true));
	     }	
		$this->getForm();
			 
  }

	public function edit() {
		$this->load->language('storesubscription/storesubscription');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('storesubscription/storesubscription');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_storesubscription_storesubscription->editPin($this->request->post['store_id']);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('storesubscription/storesubscription', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
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
			'href' => $this->url->link('storesubscription/storesubscription_form', 'user_token=' . $this->session->data['user_token'], true)
			
		);
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		
		if (isset($this->request->post['commission_percentage_card'])) {
			$data['commission_percentage_card'] = $this->request->post['commission_percentage_card'];
		}elseif (isset($store_info['commission_percentage_card'])) {
			$data['commission_percentage_card'] = $store_info['commission_percentage_card'];
		} else {
			$data['commission_percentage_card'] = '';
		}
		
		var_dump($data['commission_percentage_card']);

		if (isset($this->request->post['commission_percentage_cod'])) {
			$data['commission_percentage_cod'] = $this->request->post['commission_percentage_cod'];
		}elseif (isset($store_info['commission_percentage_cod'])) {
			$data['commission_percentage_cod'] = $store_info['commission_percentage_cod'];
		} else {
			$data['commission_percentage_cod'] = '';
		}

		var_dump($data['commission_percentage_cod']);

	////changes done on 28-06-2021 for store dropdown


		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}
				
		if (isset($this->request->post['store_id'])) {
			$data['store_id'] = $this->request->post['store_id'];
		} elseif (!empty($store_info)) {
			$data['store_id'] = $store_info['store_id'];
		} else {
			$data['store_id'] = '';
		}	
		
		var_dump($data['store_id']);

  ////changes done on 28-06-2021 for tax rate dropdown


  $this->load->model('localisation/tax_class');

  $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

  if (isset($this->request->post['GST_commission_percentage'])) {
	  $data['tax_class_id'] = $this->request->post['GST_commission_percentage'];
  } elseif (!empty($gst_info)) {
	  $data['tax_class_id'] = $gst_info['GST_commission_percentage'];
  } else {
	  $data['tax_class_id'] = 0;
  }
  var_dump($data['tax_class_id']);



//   $results = $this->model_storesubscription_storesubscription->getStoreSubscription($data);
//   var_dump($results);

		// foreach ($results as $result) {
		// 	$data['store_subscription'][] = array(
		// 	'store_subscription_id' => $result['store_subscription_id'],
		// 		'commission_percentage_card'      => $result['commission_percentage_card'],
		// 		'commission_percentage_cod'    => $result['commission_percentage_cod'],
		// 		'store'      => $result['store_id'] ? $result['store'] : $this->language->get('text_select'),
		// 		'GST_commission_percentage'   => $result['GST_commission_percentage'],
		// 		//'edit'       => $this->url->link('storesubscription/storesubscription/edit', 'user_token=' . $this->session->data['user_token'] . '&store_subscription_id=' . $result['store_subscription_id'] . $url, true)
		// 	);
		// }

        $data['user_token'] = $this->session->data['user_token'];
        $data['header']=$this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('storesubscription/storesubscription_form', $data));
	}
	
	 protected function validateForm()
	 {
	 	
	  
	 }	
}



















