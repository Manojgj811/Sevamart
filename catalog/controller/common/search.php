<?php
class ControllerCommonSearch extends Controller {
	public function index() {
		$this->load->language('common/search');

		$data['text_search'] = $this->language->get('text_search');

		if (isset($this->request->get['search'])) {
			$data['search'] = $this->request->get['search'];
		} else {
			$data['search'] = '';
		}

		//var_dump($data['search']);

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_name');
		} 
		$data['config_name'] = $this->config->get('config_name');

		
		


		return $this->load->view('common/search', $data);
	}
}