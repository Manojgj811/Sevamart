<?php
    class ControllerAccountStores extends Controller 
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
                $this->load->model('account/customer');
		        $this->load->model('account/address');
                $this->load->model('account/favouritestore');            

		$customer_group = $this->model_account_customer->getCustomer($this->customer->getId());
	    $customerid=$customer_group['address_id'];
	
	$data['customer_firstname'] = $this->customer->getFirstName();
    $data['customer_firstname'] = html_entity_decode($this->customer->getFirstName(), ENT_QUOTES, 'UTF-8');

   
   $storeresults = $this->model_setting_store->getStores();
   $customeraddress=$this->model_account_address->getAddresses();
  

        $whishresultss = $this->model_account_favouritestore->getWishlist();
      //  var_dump( $whishresultss);
        $logoresult=$this->model_setting_setting->getSetting('config', $whishresultss);
        
        foreach ($whishresultss as $resultfav) 
        {
            $whishresults=$resultfav['store_id'];
            $logoresult=$this->model_setting_setting->getSetting('config',$whishresults);

            
            $pincoderesults =$this->model_servicearea_servicearea->getPin1($whishresults);
          //  var_dump( $pincoderesults);
            $pincodevalue = [];	 

         foreach ($pincoderesults as $result)
          {
                $pincodevalue[]= $result['pincode_no'];                       
          }
        //   var_dump( $pincodevalue);
        foreach($customeraddress as $resultaddress)
        {
                $addresspostcode=$resultaddress['postcode'];	
        }
       /// var_dump($resultaddress['postcode']);
        for($pinresult=0; $pinresult<count($pincodevalue);$pinresult++)
        {   
         if($addresspostcode==$pincodevalue[$pinresult])
        // var_dump( $pincodevalue[$pinresult]);  
         { 
            if($logoresult)
             { 
                $data['storename']=$logoresult['config_name'];
                $data['Storeowner']=$logoresult['config_owner'];
                $data['Storeurl']=$logoresult['config_url'];
                
                    if (is_file(DIR_IMAGE . $logoresult['config_logo'])) 
                    {
                        $data['storelogo'] = $this->model_tool_image->resize($logoresult['config_logo'], 180, 140);
        
                    } 
                    $a=$logoresult['config_url'];

                    $b=$a."index.php?route=common/store";

                 $data['wishliststores'][]=array(
                'id'=>$resultfav['store_id'],
                'name' =>  $data['storename'],
                'url'=> $data['storelogo'],
                'href' => $b
            );

            }
        }
    }
}



		$favouriteStores = [];
        $whishresults = $this->model_account_favouritestore->getWishlist();
        foreach ($whishresults as $resultfav) 
        {
            $favouriteStores[]=$resultfav['store_id'];
           
        }
      
       
            foreach ($storeresults as $result) 
            {
            $storeid=$result['store_id']; 
            $storenames=$result['name'];
          
            if(!in_array($storeid, $favouriteStores))
             {  
                 
            $pincoderesults =$this->model_servicearea_servicearea->getPin($storeid);
                $pincodevalue = [];	 

             foreach ($pincoderesults as $result)
              {
                    $pincodevalue[]= $result['pincode_no'];                       
              }
             
            foreach($customeraddress as $resultaddress)
            {
                    $addresspostcode=$resultaddress['postcode'];	
            }
                   
              $logoresult=$this->model_setting_setting->getSetting('config', $storeid);
              $storeresults = $this->model_setting_store->getStores();
                 
    
                 for($pinresult=0; $pinresult<count($pincodevalue);$pinresult++)
                 {   
                  if($addresspostcode==$pincodevalue[$pinresult])
                 // var_dump( $pincodevalue[$pinresult]);  
                  { 
                    if($logoresult)
                      { 
                                $data['storename']=$logoresult['config_name'];
                                $data['Storeowner']=$logoresult['config_owner'];
                                $data['Storeurl']=$logoresult['config_url'];
                                $storeid=$result['store_id']; 
                                
                                if (is_file(DIR_IMAGE . $logoresult['config_logo'])) 
                                {
                                    $data['storelogo'] = $this->model_tool_image->resize($logoresult['config_logo'], 180, 140);
                                 } 

                                $a=$logoresult['config_url'];

                                $b=$a."index.php?route=common/store";
                      
                                    $data['stores'][] = array(
                                        'name' =>  $data['storename'],
                                        'url'=> $data['storelogo'],
                                        'href' =>$b,
                                        'id'=>$result['store_id']
                                    );
                                }
                            } 
                        } 
                    }
                 }    
        $data['wishlist'] =$this->load->controller('account/favouritestore');
        $data['homepageheader'] =$this->load->controller('common/homepageheader');
        $data['pincodesearch'] =$this->load->controller('common/pincodesearch');
        $data['footer'] =$this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('account/stores',$data));
     
    }
 }
    
