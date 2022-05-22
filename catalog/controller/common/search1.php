<?php
class ControllerCommonSearch1 extends Controller {
	public function index() {
		$this->load->language('common/search1');

		$data['text_search'] = $this->language->get('text_search');

		if (isset($this->request->get['search1'])) {
			$data['search1'] = $this->request->get['search1'];
		} else {
			$data['search1'] = '';
		}

		return $this->load->view('common/search1', $data);
	}
}