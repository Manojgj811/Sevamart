<?php
class ControllerCommonPincodeSearch extends Controller {
	public function index() {
		$this->load->language('common/pincodesearch');

		$data['text_search'] = $this->language->get('text_search');

		if (isset($this->request->get['pincodesearch'])) {
			$data['pincodesearch'] = $this->request->get['pincodesearch'];
		} else {
			$data['pincodesearch'] = '';
		}
//var_dump($data['pincodesearch']);
		return $this->load->view('common/pincodesearch', $data);
	}
}