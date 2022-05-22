<?php
class ControllerAccountFavouriteStore extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/favouritestore', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/favouritestore');

		$this->load->model('account/favouritestore');

		$this->load->model('setting/store');

		$this->load->model('tool/image');
		
		
	}

	public function add() {
		$json = array();
		$this->load->model('setting/store');
		if (isset($this->request->post['store_id'])) {
			$store_id = $this->request->post['store_id'];		
		} else {
			$store_id = 0;
		}
	//	var_dump("getting storeid " .$store_id  );
		$store_info = $this->model_setting_store->getStores($store_id);
		if ($store_info) {
			if ($this->customer->isLogged()) {
					
				// Edit customers cart
				$this->load->language('account/stores');
				$this->load->model('account/favouritestore');

			 $this->model_account_favouritestore->addWishlist($this->request->post['store_id']);
			 $json['success'] = $this->language->get('favourite_added');
				$json['success'] = sprintf($this->language->get('favourite_added'), $this->url->link('account/store', 'store_id=' . (int)$this->request->post['store_id']), $this->url->link('account/favouritestore'));
	
				$json['total'] = sprintf($this->language->get('text_wishlist'), $this->model_account_favouritestore->getTotalWishlist());
			} else {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}

				$this->session->data['wishlist'][] = $this->request->post['store_id'];
				$this->session->data['wishlist'] = array_unique($this->session->data['wishlist']);
				$json['success'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('account/store', 'store_id=' . (int)$this->request->post['store_id']), $this->url->link('account/favouritestore'));
				$json['total'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
			}		
	}
	//$data['stores'] = array();
	$results = $this->model_account_favouritestore->getWishlist();
	foreach ($results as $result) {
		$store_info = $this->model_setting_store->getStores($result['store_id']);		
	}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		
		$this->load->language('account/stores');
		$this->load->model('account/favouritestore');
		$this->load->model('setting/store');
		$json = array();
		if (isset($this->request->post['store_id'])) {
			//var_dump($this->request->post['store_id']);
			// Remove Wishlist
			$this->model_account_favouritestore->deleteWishlist($this->request->post['store_id']);
			$json['success'] = $this->language->get('favourite_remove');
			$this->session->data['success'] = $this->language->get('favourite_remove');

		//	$this->response->redirect($this->url->link('account/favouritestore'));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	
	}


	
}
