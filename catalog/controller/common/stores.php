<?php
    class ControllerCommonStores extends Controller 
    {
        public function index() 
        {
            $this->load->language('common/stores');

                $this->document->setTitle($this->language->get('config_meta_title'));
                $this->document->setDescription($this->language->get('config_meta_description'));
                $this->document->setKeywords($this->language->get('config_meta_keyword'));

        
        $this->load->model('setting/store');
        $this->load->model('setting/setting');
        $this->load->model('tool/image');
        $this->load->model('servicearea/servicearea');


                //storename search
            if (isset($this->request->get['storenamesearch'])) {
                $data['storenamesearch'] = $this->request->get['storenamesearch'];
            } else {
                $data['storenamesearch'] = '';
            }
            $storenamesearch = $data['storenamesearch'];
           
                //pincode search

                        if (isset($this->request->get['pincodesearch'])) {
                            $data['pincodesearch'] = $this->request->get['pincodesearch'];
                        } else {
                            $data['pincodesearch'] = '';
                        }   
                        $pincodesearch = $data['pincodesearch'];

      if( $pincodesearch)
    {
       
        $storeresults= $this->model_setting_store->getStores();
        foreach( $storeresults  as $result)
        {
        $storeid=$result['store_id'];
        $pincoderesults =$this->model_servicearea_servicearea->getPin($storeid);
    
             $emptyArray = [];

            foreach ($pincoderesults as $pincode) 
             { 
               $emptyArray[]= $pincode['pincode_no']; 
             }
        $logoresult=$this->model_setting_setting->getSetting('config', $storeid);
            if (in_array($pincodesearch,$emptyArray))
            {
                if($logoresult)
                {
                $data['storename']=$logoresult['config_name'];
                $data['Storeowner']=$logoresult['config_owner'];
            
                if (is_file(DIR_IMAGE . $logoresult['config_logo'])) 
                {
                    $data['storelogo'] = $this->model_tool_image->resize($logoresult['config_logo'], 100, 80);
                } 
                

                $data['stores'][] = array(
                    'name' => $result['name'],
                    'url'=> $data['storelogo'],
                    'href' => $result['url'],
                    'id'=>$result['store_id']
                );

                
               }
            }
        }
    }
    else
        {   
            $storesearchnameresults= $this->model_setting_store->getStoresname($storenamesearch);
            foreach( $storesearchnameresults  as $result)
            {
            $storeid   = $result['store_id'];
            $storename = $result['name'];
            if($storesearchnameresults)
            {
               
            }
            $logoresult=$this->model_setting_setting->getSetting('config', $storeid);
               if($logoresult)
                    {
                    $data['storename']=$logoresult['config_name'];
                    $data['Storeowner']=$logoresult['config_owner'];
                
                    if (is_file(DIR_IMAGE . $logoresult['config_logo'])) 
                    {
                       // unset($storeresults);
                        $data['storelogo'] = $this->model_tool_image->resize($logoresult['config_logo'], 100, 80);
                    } 
                     $a=$result['url'];
   
                     $b=$a."index.php?route=common/store";
                     $data['stores'][] = array(
                        'name' => $result['name'],
                        'url'=> $data['storelogo'],
                        'href' => $b,
                        'id'=>$result['store_id']
                    );
					 
                 }
              } 
       }

    
            $data['login'] =$this->load->language('account/login');
            $data['homepageheader'] =$this->load->controller('common/homepageheader');
            $data['pincodesearch'] =$this->load->controller('common/pincodesearch');
            $data['storenamesearch'] =$this->load->controller('common/storenamesearch');
            $data['footer'] =$this->load->controller('common/footer');
        
           $a=$this->response->setOutput($this->load->view('common/stores',$data));
    }
}
    ?>