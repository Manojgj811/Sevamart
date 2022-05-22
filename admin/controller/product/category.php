<?php
class ControllerProductCategory extends Controller {
	public function index() {
		//$this->load->language('product/category');

				/// newly loaded
		$this->load->model('sale/poscategory');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$this->load->model('user/user');

		if ($this->request->server['HTTPS']) 
		{
		$data['base'] = HTTPS_SERVER;
		} else {
		$data['base'] = HTTP_SERVER;
		}

		$data['user_token']=$this->session->data['user_token'];

		//var_dump($data['user_token']);
				
		$user_info = $this->model_user_user->getUser($this->user->getId());

		$storemanager_storeID=$user_info['store_id'];

		$data['store_id']=$storemanager_storeID;

        if(isset($this->session->data['TelephoneNumber'])) 
		{ 
			
		    $poscustomerTelephoneNumber= $this->session->data['TelephoneNumber'];
				
			$this->load->model('sale/poscustomer');

			$posrecord= $this->model_sale_poscustomer->getPosCustomerId($poscustomerTelephoneNumber);  

			//var_dump(($posrecord));

			$chktheid= $posrecord['customer_id'];

			$this->session->data['poscustomerid'] =  $chktheid;     

			//echo "<br>";

			$data['posCustomerId']=$this->session->data['poscustomerid'];

			$customername=$this->model_sale_poscustomer->getPosCustomerByPhone($poscustomerTelephoneNumber);
		
		$data['loginposcustomername']=$customername['firstname'];

    		

		}

	
		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			//'href' => $this->url->link('common/store')
		);

		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

          $parts = explode('_', (string)$this->request->get['path']);

		//	var_dump(	$parts);

			$array=array( $parts);
			$value=array_values($array);

			//var_dump($value);

			//echo "<br>";

			//var_dump($value[0]);

		$this->session->data['poscategorylink']= implode( '_', $value[0]);

    		$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				// changed here
				$category_info =$this->model_sale_poscategory->getCategory($path_id,$storemanager_storeID);

				if ($category_info) {
					// $data['breadcrumbs'][] = array(
					// 	'text' => $category_info['name'],
					// 	'href' => $this->url->link('product/category', 'path=' . $path . $url)
					// );
				}
			}

		} 
		else {
			$category_id = 0;
		}

			$category_info =$this->model_sale_poscategory->getCategory($category_id, $storemanager_storeID);

		//	var_dump($category_info);
			
		
		
		if ($category_info) {
			
			//echo "yes ";
			$this->document->setTitle("Category Info");
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);

			$data['heading_title'] = $category_info['name'];

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			// Set the last category breadcrumb
			// $data['breadcrumbs'][] = array(
			// 	'text' => $category_info['name'],
			// 	'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			// );

			if ($category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');

			$data['compare'] = $this->url->link('product/compare');

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['categories'] = array();

		//	$results = $this->model_catalog_category->getCategories($category_id);

		$results = 	$this->model_sale_poscategory->getCategories($category_id,	$storemanager_storeID);

				//echo "check";
				//echo "<br>";
				//var_dump($results);

		foreach ($results as $result) {
		
		$filter_data = array(
			'filter_category_id'  => $result['category_id'],
			'filter_sub_category' => true
		);

		$data['categories'][] = array(
			'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_sale_poscategory->getTotalProducts($filter_data,$storemanager_storeID ) . ')' : ''),
			'href' => $this->url->link('product/category&user_token='.$this->session->data['user_token'], '&path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
	
			// 'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
			
		
		);
				
	}

			$data['products'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

			//echo "<br>";

			// var_dump($filter_data);

			$product_total =$this->model_sale_poscategory->getTotalProducts($filter_data,$storemanager_storeID );

		//	var_dump($product_total);

			//echo "<br>";

			//var_dump(count($product_total));

			$productId=array();

			$a =gettype($productId);
			//echo "the type is $a";
	
		//$results = $this->model_catalog_product->getProducts($filter_data);

		$posresults = $this->model_sale_poscategory->getProducts($filter_data , $storemanager_storeID);

        //var_dump($posresults);

	foreach ($posresults as $result)
	{
		if ($result['image']) 
		{
			$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
		}
		else
		{
		$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
		}

		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		} else {
			$price = false;
		}

		if ((float)$result['special']) {
			$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		} else {
			$special = false;
		}

		if ($this->config->get('config_tax')) {
			$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
		} else {
			$tax = false;
		}

		if ($this->config->get('config_review_status')) {
			$rating = (int)$result['rating'];
		} else {
			$rating = false;
		}

				
		   $data['products'][] = array(

					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					//
					'quantity'  => $result['quantity'],
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
				 
				);
			

		       }


				
			///////////////////  	/start

        $poscartrecord=$this->model_sale_poscustomer->getPosProductsForSingleCart($storemanager_storeID , $chktheid  );

			// var_dump($poscartrecord);

		      $data['myVariable']=array();

				foreach($poscartrecord as $cart)
				{ 
				$product_info=$this->model_catalog_product->getProduct($cart['product_id']);

				if($product_info['product_id']==$cart['product_id'])
				{
						//echo "test array ";

					//echo "<br>";

					$data['myVariable'][]=array($cart['product_id']);

						//echo  $data['myVariable'][0];

						//$data['myVariable'][]=$cart['product_id'];

						$data['openids'][]=array(
						'product_id'=> $cart['product_id'],
						'quantity'=> $cart['quantity'],
						'cart_id'=> $cart['cart_id']
						);

					    $data['test'][]=[$cart['product_id']];

                  }


				$array = array( $cart['product_id'] );

				$allKeys = array_values($array);

		$data['key'][]=$allKeys[0];

			 }

		 	$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination = new Pagination();

			$pagination->total = $product_total;
			
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'canonical');
			} else {
				$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. $page), 'canonical');
			}
			
			if ($page > 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1)), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			//////common/home changed to common/store
			$data['continue'] = $this->url->link('common/store');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$data['search'] = $this->load->controller('sale/possearch');
	
			$data['cart'] = $this->load->controller('sale/poscart');
		
			
			$data['menu'] = $this->load->controller('sale/posmenu');


			$this->response->setOutput($this->load->view('product/category', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/category', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['continue'] = $this->url->link('common/store');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	    //////////

}
