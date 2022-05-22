<?php
class ControllerCommonForgotten extends Controller {
	private $error = array();

	public function index()
     {
		if ($this->user->isLogged() && isset($this->request->get['user_token']) && ($this->request->get['user_token'] == $this->session->data['user_token'])) 
        {
			$this->response->redirect($this->url->link('common/dashboard', '', true));
		}
        	$this->response->setOutput($this->load->view('common/forgotten', $data));
	}

	
}