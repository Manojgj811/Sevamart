<?php
class ControllerServiceareaServicearea extends Controller 
{
	private $error = array();
	
	public function index() {
			
		$this->load->language('servicearea/servicearea');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('servicearea/servicearea');			

		$this->load->model('user/user');
	
		$user_info = $this->model_user_user->getUser($this->user->getId());
			if($user_info)
		  {
			$data['storeid']= $user_info['store_id'];
			$storeid=$data['storeid'];
	

			if(($this->request->server['REQUEST_METHOD'] == 'POST')&& $this->validateForm())  
			{	
				$this->model_servicearea_servicearea->deletePin($storeid); 	
      			$store_id=$this->model_servicearea_servicearea->addPin($this->request->post ,$storeid);     
				
	 			$this->session->data['success'] = $this->language->get('text_success');
	 			$this->response->redirect($this->url->link('servicearea/servicearea', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}	
	     }	
		$this->getForm();
			 
  }

	public function edit() {
		$this->load->language('servicearea/servicearea');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('servicearea/servicearea');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_servicearea_servicearea->editPin($this->request->post['store_id']);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('servicearea/servicearea', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['store_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['pincode_no'])) {
			$data['error_name'] = $this->error['pincode_no'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['delivery_charges'])) {
			$data['error_name'] = $this->error['delivery_charges'];
		} else {
			$data['error_name'] = array();
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(		
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('servicearea/servicearea_form', 'user_token=' . $this->session->data['user_token'], true)
			
		);
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

	  $user_info = $this->model_user_user->getUser($this->user->getId());

	$filter_total = $this->model_servicearea_servicearea->getTotalPin();
	
	  $results = $this->model_servicearea_servicearea->getPin($user_info['store_id']); 
	 // var_dump($results[0]['title']); 
	  foreach ($results as $result) {
		  $data['option_values'][] = array(
			  'pincode_no'      => $result['pincode_no'],
			  'delivery_charges' => $result['delivery_charges'],
			  'title' => $result['title']

		  );
	  }
	 
	  
$this->load->model('localisation/tax_class');

$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();


if (isset($this->request->get['tax_class_id'])) {
  $data['tax_class_id'] = $this->request->get['tax_class_id'];
} elseif (!empty($store_contract_info )) {
 $data['tax_class_id'] = $store_contract_info ['tax_class_id'];
} else {
  $data['tax_class_id'] = 0;
}
        $data['user_token'] = $this->session->data['user_token'];
        $data['header']=$this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('servicearea/servicearea_form', $data));
	}
	
	 protected function validateForm()
	 {
	 	
	  	if(!$this->user->hasPermission('modify', 'servicearea/servicearea')) 
	  	 {
		
	 	 	$this->error['warning'] = $this->language->get('error_permission');
   	  	 }
			foreach ($this->request->post['servicearea'] as  $value) {	
				if ((utf8_strlen($value['pincode_no']) < 6) || !is_numeric($value['pincode_no']) || (utf8_strlen($value['pincode_no']) > 6)) {
					$this->error['pincode_no'] = $this->language->get('error_name');
			}

				if ((utf8_strlen($value['delivery_charges']) < 1) || (utf8_strlen($value['delivery_charges']) > 255)) {
					$this->error['delivery_charges'] = $this->language->get('error_name');
				}
		}
	    if ($this->error && !isset($this->error['warning'])) {
	 	$this->error['warning'] = $this->language->get('error_warning');
	 }
	 return !$this->error;
	 }	
}







