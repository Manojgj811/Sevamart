<?php
class ControllerCommonHome extends Controller 
{ 
	public function index() 
	{
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		$this->load->model('setting/store');
		$this->load->model('setting/setting');
		$this->load->model('tool/image');

        if(isset($this->request->get['route'])) 
		{
        $var= $this->request->get['route'];
       // var_dump($var);
		$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

      $csid = $this->config->get('config_store_id');

      $data['cureentstoreid']=  $csid;
        // echo $csid;

        $storeresults= $this->model_setting_store->getStores();

         foreach($storeresults as $result)
        {
         $storeid=$result['store_id'];
         $name= $result['name'];

         $logoresult=$this->model_setting_setting->getSetting('config', $storeid);

         //var_dump($logoresult);

            if($logoresult)
            {
                $data['storename=']=$logoresult['config_name'];
                $data['Storeowner']=$logoresult['config_owner'];
            
                // $data['storelogo']=$logoresult['config_logo'];
    
                if (is_file(DIR_IMAGE . $logoresult['config_logo'])) 
                {
                  $data['storelogo'] = $this->model_tool_image->resize($logoresult['config_logo'], 110, 80);
                //echo "s";
                } 

                //a+=b;
                  $a=$result['url'];

                  $b=$a."index.php?route=common/store";

              
                     $data['stores'][]=array(
                    'name'=>$result['name'],
                    'url'=>$data['storelogo'],
                    'href'=>$b,
				    'id'=>$result['store_id']
                );
                
            }  
            
        }  


     


     $data['column_left']=$this->load->controller('common/column_left');
		$data['column_right']=$this->load->controller('common/column_right');
		$data['content_top']=$this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
	    //$data['header'] = $this->load->controller('common/header');
		//newly added homepageheader and search 1 for pincode search purpose
		$data['homepageheader'] =$this->load->controller('common/homepageheader');
        //$data['pincodesearch'] =$this->load->controller('common/pincodesearch');
		//$data['storenamesearch'] =$this->load->controller('common/storenamesearch');
	
       $this->response->setOutput($this->load->view('common/stores', $data));


        }
    }
