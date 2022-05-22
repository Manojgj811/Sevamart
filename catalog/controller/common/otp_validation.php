<?php
class ControllerCommonOtpValidation extends Controller {
public function index() {

    // if (!$this->customer->isLogged()) {
    //     $this->session->data['redirect'] = $this->url->link('account/return', '', true);

    //     $this->response->redirect($this->url->link('account/login', '', true));
    // }
    
   
    if($this->request->server['REQUEST_METHOD'] == 'POST'){
     //  var_dump($this->request->post);
      
		  // Unset guest
          $json = array();
		if($_COOKIE['sevaMartLoginOtp'] != $this->request->post['enteredOtp']){
         
			$data['error_warning'] = "Invalid OTP";
			//$data['mobile'] = $this->request->post['mobile'];
            $json['response'] = "Invalid Otp";
            
		}
        else{

            $json['response'] = "Valid Otp";
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    else{
		$this->load->language('common/otp_validation');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/store')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('common/otp_validation', '', true)
		);

		$data['continue'] = $this->url->link('common/store');
        $data['action'] = $this->url->link('common/otp_validation', '', true);
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['homepageheader'] = $this->load->controller('common/homepageheader');

		$this->response->setOutput($this->load->view('common/otp_validation', $data));
    }
  

}

}