<?php
class ControllerCommonHeader extends Controller {
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

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
			//var_dump(SERVER_URL);
			//return;
		}
		

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_store_id');
		} 
		$data['config_store_id'] = $this->config->get('config_store_id');
		
	
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

		//$data['name'] = $this->config->get('config_name');
		$csid=$data['config_store_id'];
	//	echo $csid;

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
			$res=$data['text_wishlist'];
			//var_dump($res);
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged']= sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
        // changes for displaying customer namespace

		$this->load->model('account/customer');

		$this->load->model('account/address');

		$customer_group = $this->model_account_customer->getCustomer($this->customer->getId());
	

		$data['customer_firstname'] = $this->customer->getFirstName();

		$data['customer_firstname'] = html_entity_decode($this->customer->getFirstName(), ENT_QUOTES, 'UTF-8');

   // to display favouritestores in catalog header

   $this->load->model('setting/store');
   $this->load->model('account/favouritestore');

   $whishresultss = $this->model_account_favouritestore->getWishlist();
   $data['store_id'] = $this->model_account_favouritestore->getWishlist();
   $customeraddress=$this->model_account_address->getAddresses();

		$data['favstore']=false;
		$whishresultstore=[];

		foreach ($whishresultss as $result) 
		{
			$whishresultstore[] = $result['store_id'];
		}
			
		if (in_array ($data['config_store_id'],$whishresultstore ))
		{
			$data['favstore']=true;
		} 

		// $csid=$data['config_store_id'];
		// echo $csid;


      	$data['home'] = $this->url->link('common/store');
		$data['href'] = SERVER_URL;
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['return'] = $this->url->link('account/return', '', true);
		$data['cancel'] = $this->url->link('account/cancel', '', true);
		$data['item_not_received'] = $this->url->link('account/item_not_received', '', true);
		$data['track_items'] = $this->url->link('account/track_items', '', true);
		
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		return $this->load->view('common/header', $data);
	}
	
}
