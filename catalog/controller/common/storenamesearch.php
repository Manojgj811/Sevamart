<?php
class ControllerCommonStorenameSearch extends Controller {
	public function index() {
		$this->load->language('common/storenamesearch');

		$data['text_search'] = $this->language->get('text_search');

		if (isset($this->request->get['storenamesearch'])) {
			$data['storenamesearch'] = $this->request->get['storenamesearch'];
		} else {
			$data['storenamesearch'] = '';
		}
       
		return $this->load->view('common/storenamesearch', $data);
	}
}