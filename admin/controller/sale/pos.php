
<?php
	 
	 class ControllerSalePos extends Controller {
   
       private $error = array();
   
       public function index() {
   
       $this->load->language('sale/order');
       $this->load->model('user/user');
       $this->load->model('sale/poscustomer');
	   $this->load->model('sale/poscategory');
   
       $data['title']='title';
    
        $this->document->setTitle($this->language->get('heading_title'));
    
        $data['user_token']=$this->session->data['user_token'];
        $user_info = $this->model_user_user->getUser($this->user->getId());
         
           if (($this->request->server['REQUEST_METHOD'] == 'POST')) 
            {
                $this->model_sale_poscustomer->clearCartProducts($user_info['store_id'], $this->session->data['customer_ids']);

                 unset($this->session->data['customer_ids']);
				 
                 $data['productsInCart'] = false;
            }
    
           if(isset($this->session->data['customer_ids']))
           {  
       
           $cartProducts = $this->model_sale_poscustomer->getPosProductsForSingleCart($user_info['store_id'], $this->session->data['customer_ids']);
               
           //$cartProducts = $this->model_sale_poscustomer->getPosProductsForSingleCart($user_info['store_id'], $chktheid);
               
               if(count($cartProducts) > 0)
               {
                   $data['productsInCart'] = true;
               }
               else
               {
               $data['productsInCart'] = false;
   
               }
               
           }

			if(isset( $this->session->data['customername'] ))
			{
				$data['customername']=$this->session->data['customername'];
			}

		    $data['header']=$this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $data['search'] = $this->load->controller('sale/possearch');
            $data['menu'] = $this->load->controller('sale/posmenu');
			$data['cart'] = $this->load->controller('sale/poscart');
			
            $data['add'] = $this->url->link('sale/pos/addCustomer', 'user_token=' . $this->session->data['user_token'], true);
             $data['clearCart'] = $this->url->link('sale/pos', 'user_token=' . $this->session->data['user_token'], true);
            $data['hrefDashboard'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);

            $this->response->setOutput($this->load->view('sale/pos', $data));
    
           }
    
		public function addCustomer() 
		{
		
			$this->load->model('sale/poscustomer');
			$this->load->model('user/user');
			$this->load->model('sale/poscategory');
			$this->load->model('tool/image');
		    $this->load->model('catalog/product');
			$this->load->model('tool/upload');

			$user_info = $this->model_user_user->getUser($this->user->getId());

			$storemanager_storeID=$user_info['store_id'];

			$data['store_id']=$storemanager_storeID;

			$this->session->data['store_id'] = $storemanager_storeID;

			$data['user_token']=$this->session->data['user_token'];

			if (($this->request->server['REQUEST_METHOD'] == 'POST')) 
			{
			
				$customer_id= $this->model_sale_poscustomer->addCustomer($this->request->post, $storemanager_storeID);

				$customer_info= $this->model_sale_poscustomer->getPosCustomerByPhone($this->request->post['Number']);
			}
    
			if ($customer_info['firstname'])
			{
			$data['customername'] = $customer_info['firstname'] . $customer_info['lastname'];
			}
				else
			{
			$data['customername'] = '';
			}

			if ($customer_info['telephone'])
			{
			$data['Number'] = $customer_info['telephone'];
			}
			else 
			{
				$data['Number'] = '';
			}
		
		$data['Name']=$data['customername'] ;

		$this->session->data['customername'] = $data['Name'];

		//var_dump($this->session->data['customername']);

		$data['TelephoneNumber'] = $data['Number'];

		$phoneno=$data['TelephoneNumber'];

		$this->session->data['TelephoneNumber'] = $phoneno;

		$sessiontelephone=$this->session->data['TelephoneNumber'];
    
        //   echo $this->session->data['TelephoneNumber'];
    
		$customerRecord = $this->db->query( "SELECT `customer_id` FROM `oc_customer` WHERE telephone= '".$phoneno."' ");

		$posrecord= $this->model_sale_poscustomer->getPosCustomerId($sessiontelephone);  

		$chktheid= $posrecord['customer_id'];

		$data['posCustomerId']=$chktheid;

		//var_dump($data['posCustomerId']);

	//return;

	$this->session->data['poscustomerid'] =  $chktheid;

	$this->session->data['customer_ids'] =  $chktheid;

          ///// to show latest products of store

		  $poslatestproducts = $this->model_sale_poscategory->getLatestProducts( $storemanager_storeID);

			foreach( $poslatestproducts as $latestProduct)
			{

				if ($latestProduct['image']) 
				{
					$image = $this->model_tool_image->resize($latestProduct['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
				else
				{
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}

				if ($this->session->data['TelephoneNumber']) 
				{
                // $price =   version_compare(VERSION, '3.0.3.6', '>=') ? $this->currency->format($this->tax->calculate($latestProduct['price'], $latestProduct['tax_class_id']), $this->session->data['currency'] , '1.0000000', true)   : $this->currency->format($latestProduct['price']);
				
				$price =    $this->currency->format($this->tax->calculate($latestProduct['price'], $latestProduct['tax_class_id']),$this->config->get('config_currency'));
				

			    } 

				else
				 {
						
					$price = false;
					
				}

				$data['newproducts'][]=array(
				'product_id'  => $latestProduct['product_id'],
				'name'  => $latestProduct['name'],
				'quantity'  => $latestProduct['quantity'],
				'minimum'     => $latestProduct['minimum'] > 0 ? $latestProduct['minimum'] : 1,
				'image'=>$image,
				//'description'=>$latestProduct['description'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($latestProduct['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'=>$price,
				'date_added'=>$latestProduct['date_added']

			);
	
		}

			$poscartrecord=$this->model_sale_poscustomer->getPosProductsForSingleCart($storemanager_storeID , $chktheid  );

		//	var_dump($poscartrecord);

		
			
			$data['myVariable']=array();

			  foreach($poscartrecord as $cart)
			  { 
			  $product_info=$this->model_catalog_product->getProduct($cart['product_id']);

			  if($product_info['product_id']==$cart['product_id'])
			  {
					  $data['myVariable'][]=array($cart['product_id']);

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

            $data['header']=$this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $data['search'] = $this->load->controller('sale/possearch');
            $data['cart'] = $this->load->controller('sale/poscart');
            $data['menu'] = $this->load->controller('sale/posmenu');
    
            $this->response->setOutput($this->load->view('sale/poslogin', $data)); 

	 
		   }


	  public function existingNumber(){
    
                //var_dump($this->request->post);
			$json = array();
			$this->load->model('sale/poscustomer');

			$customer_info = $this->model_sale_poscustomer->getPosCustomerByPhone($this->request->post['telephone']);

			if($customer_info){
				$json['success'] = true;
				$json['name'] = $customer_info['firstname'];
				// var_dump($customer_info);
			}

			else
			{
				$json['success'] = false;
				// var_dump("nodata");
			}
        
                  $this->response->addHeader('Content-Type: application/json');
                  $this->response->setOutput(json_encode($json));
    
            }


	  }
    
        
        
        
        
        
        
        
        
        
        
    
            
            
    