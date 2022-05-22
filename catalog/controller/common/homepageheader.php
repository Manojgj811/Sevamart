<?php
class ControllerCommonHomepageHeader extends Controller {
	public function index() { 
		// Analytics
		$this->load->model('setting/extension');
		$this->load->model('servicearea/servicearea');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) 
		{
			$server = $this->config->get('config_ssl');
		} 
		else
		{
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();
		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');
		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/homepageheader');

		// Wishlist
		if ($this->customer->isLogged()) 
		{
		     $this->load->model('account/wishlist');

         	 $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());

		} 

		else
		 {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		 }

		$data['text_logged']= sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true ));
		
               //  changes for displaying customer namespace

		$this->load->model('account/customer');

		$this->load->model('account/address');

		$customer_group = $this->model_account_customer->getCustomer($this->customer->getId());
	 	//$customerid=$customer_group['address_id'];
	
$data['customer_firstname'] = html_entity_decode($this->customer->getFirstName(), ENT_QUOTES, 'UTF-8');

   // to display stores in catalog header
   $this->load->model('common/shoppingcart');
   $data['customercartstore'] = $this->model_common_shoppingcart->getCustomercartstore();
   $customeraddress=$this->model_account_address->getAddresses();
   
   $output = array_intersect_key(
	$data['customercartstore'], 
	array_unique(array_map(function($result) {
		return $result['store_id'];
	},
	 $data['customercartstore']))
	);
	
 	foreach ($output  as $result) 
   {
	  $customer_id =$result['customer_id']; 
   	  $store_id    =$result['store_id']; 
      $storename   =$result['Storename'];
	  $storeurl    =$result['Storeurl'];

	  $this->load->model('setting/setting');
	  $this->load->model('setting/store');
	  $logoresult=$this->model_setting_setting->getSetting('config', $store_id);
           
                if($logoresult)
                {
                $data['storename']=$logoresult['config_name'];
                
            
                if (is_file(DIR_IMAGE . $logoresult['config_logo'])) 
                {
					$data['storelogo'] = $server . 'image/' . $logoresult['config_logo'];
                    //$data['storelogo'] = $this->model_tool_image->resize($logoresult['config_logo'], 50, 40);
					//$data['storelogo'] = $this->model_tool_image->resize($logoresult['config_logo'], 50, 40);
                } 
                
				$data['customercartstore'] = $this->model_common_shoppingcart->getCustomercartstore();

                $data['customercartstore'][] = array(
                   
                    'url'=> $data['storelogo']
                  );  

               }

			  
   
	$cartstoreurl= $storeurl ."index.php?route=common/store";
            	
	$data['customercartstore'] = $this->model_common_shoppingcart->getCustomercartstore();
	  if( $data['customercartstore']){
		

	 $data['customercartdetail'][] = array(
		'customer_id' =>$result['customer_id'],
		'store_id'    =>$result['store_id'],
		'Storename'   =>$result['Storename'],
		'Storeurl'    =>$cartstoreurl,
		'logo'        => $data['storelogo']
     );

	// var_dump($data['customercartdetail']);		 
	  }
   }

	
        $data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		//$data['register'] = $this->url->link('account/register', '', true);
		$data['register'] = $this->url->link('common/otp_validation', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
	
		$data['return'] = $this->url->link('account/return', '', true);

		$data['edit'] = $this->url->link('account/edit', '', true);
		$data['password'] = $this->url->link('account/password', '', true);
		$data['address'] = $this->url->link('account/address', '', true);
		
        $data['cancel'] = $this->url->link('account/cancel', '', true);
		$data['item_not_received'] = $this->url->link('account/item_not_received', '', true);
		$data['track_items'] = $this->url->link('account/track_items', '', true);

		$data['edit'] = $this->url->link('account/edit', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');

		$data['storenamesearch'] = $this->load->controller('common/storenamesearch');
		$data['pincodesearch'] = $this->load->controller('common/pincodesearch');
		
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		return $this->load->view('common/homepageheader', $data);
	}
}
