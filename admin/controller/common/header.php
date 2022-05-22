<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		$data['title'] = $this->document->getTitle();

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$this->load->language('common/header');
		
		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->user->getUserName());
	

		if (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token'])) {
			$data['logged'] = '';

			$data['home'] = $this->url->link('common/dashboard', '', true);
		} else {
			$data['logged'] = true;

			$data['home'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
			$data['logout'] = $this->url->link('common/logout', 'user_token=' . $this->session->data['user_token'], true);
			$data['profile'] = $this->url->link('common/profile', 'user_token=' . $this->session->data['user_token'], true);
		
			$this->load->model('user/user');
	
			$this->load->model('tool/image');
	
			$user_info = $this->model_user_user->getUser($this->user->getId());
	
			if ($user_info) 
			{
				$data['userid']= $user_info['user_id'];
				
				$data['storeid']= $user_info['store_id'];
				
				$data['firstname'] = $user_info['firstname'];
				$data['lastname'] = $user_info['lastname'];
				$data['username']  = $user_info['username'];
				$data['user_group'] = $user_info['user_group_id'];
	
				if (is_file(DIR_IMAGE . $user_info['image'])) 
				{
					$data['image'] = $this->model_tool_image->resize($user_info['image'], 75, 45);
				} 
				else 
				{
					$data['image'] = $this->model_tool_image->resize('profile.png',45, 45);
				}
			}
			 else {
				$data['firstname'] = '';
				$data['lastname'] = '';
				$data['user_group'] = '';
				$data['image'] = '';
			}			
			
			// Online Stores
			$data['stores'] = array();
			//newly created
			$data['adminstores'] = array();
              if($data['user_group']==1)
			{
		    $data['adminstore'][] = array(
				'name' => $this->config->get('config_name'),
				'href' => HTTP_CATALOG
			);
		}
			$this->load->model('setting/store');
			$this->load->model('setting/setting');

			$results = $this->model_setting_store->getStores();
			
		
			$name="sevamart";

			if($data['user_group']==1)
			{
			$data['adminstores'][]= array(
			  'p'=>	$name,
			        );
			    }  
			foreach ($results as $result) 
			{
			$storeid=$result['store_id'];

			  $logoresult=$this->model_setting_setting->getSetting('config', 	$storeid);
		
                if($logoresult)
            {
                if (is_file(DIR_IMAGE . $logoresult['config_logo'])) 
                {
                    $data['storelogo'] = $this->model_tool_image->resize($logoresult['config_logo'], 100, 70);
			   
					//var_dump($data['storelogo']);
				} 
			
				  
			
			   if($data['user_group']==1 )
		     	{
			     $data['adminstore'][] = array(
				//'name' => $this->config->get('config_name'),
			'name' => $result['name'],
			'href' => $result['url']
			);
		    }     

            if($data['storeid']==$result['store_id'] && $data['user_group']!=1)
				 {
				$data['stores'][] = array(
					'name' => $result['name'],
					'url'=> $data['storelogo'],
					'href' => $result['url']
				);
			
			}
			
			}
		}

		
		
	return $this->load->view('common/header', $data);
	}
}
}