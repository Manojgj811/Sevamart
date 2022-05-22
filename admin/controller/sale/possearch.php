<?php
class ControllerSalePosSearch extends Controller {
	private $error = array();

	public function index() {

	$this->load->language('sale/order');

	$this->document->setTitle($this->language->get('heading_title'));
	
	$data['user_token'] = $this->session->data['user_token'];

     if (isset($this->request->get['search']))
	{
	$data['search'] = $this->request->get['search'];

	//var_dump($data['search']);
	}

	else 
     {
		$data['search'] = '';
	 }

         return $this->load->view('sale/possearch', $data);


	
	}

     	
	
}
