
<?php

class ControllerSalePosCart extends Controller {
	private $error = array();

	public function index() {

		$rate_sum = 0;
        $product_total=0;
       	$this->load->language('sale/order');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('user/user');
		$this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('tool/upload');

   	    $user_info = $this->model_user_user->getUser($this->user->getId());
        $storemanager_storeID=$user_info['store_id'];
         $data['user_token']=$this->session->data['user_token'];

		//var_dump($storemanager_storeID);
       if(isset($this->session->data['TelephoneNumber'])) 
		{ 
			//echo "not set";
			$poscustomerTelephoneNumber= $this->session->data['TelephoneNumber'];
			$this->load->model('sale/poscustomer');
    
		$posrecord= $this->model_sale_poscustomer->getPosCustomerId($poscustomerTelephoneNumber);  

			$posCustomerId= $posrecord['customer_id'];  

			$this->session->data['customer_ids'] = $posCustomerId;     

			$data['posCustomerId']=$this->session->data['customer_ids'];

		$poscartrecord=$this->model_sale_poscustomer->getPosProductsForSingleCart( $storemanager_storeID , $posCustomerId );

		$pricedetails =$this->model_sale_poscustomer->getPosProductsTotalPrice( $storemanager_storeID , $posCustomerId );

		$totalprice=0;

		foreach ($pricedetails as $productprice) 
		{
			$totalprice=$productprice['productprice'] ;
		}

		//var_dump($poscartrecord);

		foreach ($poscartrecord as $product) 
			{
				$product_info=$this->model_catalog_product->getProduct($product['product_id']);

						if( $product_info['product_id']==$product['product_id'])
						{
								$unit_price=$product_info['price'];

						}

						$product_total += $product['quantity'];

						if (is_file(DIR_IMAGE .$product_info['image'])) 
						{
							$pimage = $this->model_tool_image->resize($product_info['image'], 140, 90);
						} 

						
						$total = $this->currency->format( $unit_price * $product['quantity'], $this->config->get('config_currency'));

						$amount=$unit_price * $product['quantity'];

						$rate_sum +=$amount;

		//	$grandtotal+=	$total;

						$data['posproducts'][] = array(
							//newly added product _id key
                     	'product_id' => $product_info['product_id'],
						'cart_id'   => $product['cart_id'],
						'thumb'     =>  $pimage,
						'name'      => $product_info['name'],
						'model'     =>  $product_info['model'],
						'quantity'  => $product['quantity'],
						'price'     =>  $product_info['price'],
						'total'     =>  $total
					
					       );

                 }


		   $data['checkout'] = $this->url->link('sale/poscheckout', 'user_token=' . $this->session->data['user_token'], true);

		  // echo "the product total ".	$product_total;

     	$data['text_items'] = sprintf( $product_total ."items"  .'-'.$this->currency->format($totalprice, $this->config->get('config_currency') ) );

         $data['totals']=array();
		  
			$data['totals'][] = array(
				'title' => 'Sub-Total',
				'text'  => $this->currency->format($rate_sum, $this->config->get('config_currency') )


			);

		}
			$data['model']=	$this->load->model('sale/order');
			$data['header']=$this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

	//$data['category'] = $this->load->controller('product/category');

	 return $this->load->view('sale/poscart', $data);

	}

	public function add() 
	{
       $this->load->language('sale/cart');

		$json = array();

		$product_total2=0;

		$rate_sum=0;

		$unit_price=0;
		
        if (isset($this->request->post['product_id'])) 
		{
			$product_id = (int)$this->request->post['product_id'];
      	} 
	        else 
		{
			$product_id = 0;
		}

		if (isset($this->request->post['quantity']))
		{
		$quantity = (int)$this->request->post['quantity'];
			
		} 
		else 
		{
			$quantity = 1;
		}

		if (isset($this->request->post['customerid']))
		{
		$customerid = (int)$this->request->post['customerid'];
			
		} 
		else 
		{
			$customerid = 0;
		}

		if (isset($this->request->post['store_id']))
		{
		$store_id = (int)$this->request->post['store_id'];
			
		} 
		else 
		{
			$store_id = 0;
		}

     $this->db->query("INSERT INTO " . DB_PREFIX . "cart SET  customer_id = '". (int)$customerid ."', session_id = '" . $this->db->escape($this->session->getId()) . "', product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "',  quantity = '" . (int)$quantity . "', date_added = NOW()");
		
		$this->load->model('catalog/product');

		$this->load->model('sale/poscustomer');

		
///////////telephone of pos customer
     $this->load->model('user/user');

		$user_info = $this->model_user_user->getUser($this->user->getId());

		$storemanager_storeID=$user_info['store_id'];

     	if(isset($this->session->data['TelephoneNumber'])) 
		{ 
			$poscustomerTelephoneNumber= $this->session->data['TelephoneNumber'];
        }

		$posrecord= $this->model_sale_poscustomer->getPosCustomerId($poscustomerTelephoneNumber);  

        $posCustomerId= $posrecord['customer_id'];

		$this->session->data['poscustomerid'] =  $posCustomerId;    

		$data['posCustomerId']=$this->session->data['poscustomerid'];

		$product_info=$this->model_catalog_product->getProduct($product_id);

		$poscartrecord2 = $this->model_sale_poscustomer->getPosProductsForSingleCart( $storemanager_storeID , $posCustomerId );

	    $pricedetails =	$this->model_sale_poscustomer->getPosProductsTotalPrice( $storemanager_storeID , $posCustomerId );	

	    $product_total2 += $quantity;

	    $amount=$unit_price *$product_total2;

		  $rate_sum +=$amount;

		  $q=0;

		  $unit_price2=0;

       	foreach ($poscartrecord2 as $product) 
	 		{
	            $q+=$product['quantity'];

				//$unit_price2+=$product_info['price'];

		   }

		   $totalprice=0;
		   
		   foreach ($pricedetails as $productprice) 
		   {
			  $totalprice=$productprice['productprice']  ;
            }
	

	if ($product_info) {

		if (!$json) {

			$json['success'] = "success ";

			$json['producttotal']=$quantity;

		   $json['productID']=$this->request->post['product_id'];

		  $json['productQuantity']  = $quantity;		   

			$json['total'] = sprintf( $q ."items"  .'-'.$this->currency->format( $totalprice , $this->config->get('config_currency') ) );
		   
		}

    	}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function update() 
	{
		$this->load->language('checkout/cart');

		$this->load->model('catalog/product');

		$json = array();

		if (isset($this->request->post['product_id'])) 
		{
			$product_id = (int)$this->request->post['product_id'];
		} 
			
		else 
		{
		$product_id = 0;
		}

		if (isset($this->request->post['customerid']))
		{
		$customerid = (int)$this->request->post['customerid'];
			
		} 

			if (isset($this->request->post['quantity']))
			{
			$quantity = (int)$this->request->post['quantity'];
				
			} 
			else 
			{
					$quantity = 1;
			}

			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($product_id);

		// var_dump($product_info);

	     if ($product_info) {

		if (!$json) {
			
  //	$this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = '" . (int)$quantity . "' WHERE  product_id = '" . (int)$product_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

  $this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = '" . (int)$quantity . "' WHERE  product_id = '" . (int)$product_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' ");


	  	$this->load->model('sale/poscustomer');
		
		/////////// trial
		$this->load->model('user/user');

		 $user_info = $this->model_user_user->getUser($this->user->getId());

		$storemanager_storeID=$user_info['store_id'];

		if(isset($this->session->data['TelephoneNumber'])) 
		{ 
			$poscustomerTelephoneNumber= $this->session->data['TelephoneNumber'];
		}

		$posrecord= $this->model_sale_poscustomer->getPosCustomerId($poscustomerTelephoneNumber);  

		@$posCustomerId= $posrecord['customer_id'];

		$this->session->data['poscustomerid'] =  $posCustomerId;    

		$data['posCustomerId']=$this->session->data['poscustomerid'];

		$product_info=$this->model_catalog_product->getProduct($product_id);

		 $poscartrecord3 = $this->model_sale_poscustomer->getPosProductsForSingleCart( $storemanager_storeID , $posCustomerId );

	    $json['minimum'] =	$product_info['minimum'];

	     $q=0;

			foreach ($poscartrecord3 as $product) 
			{
			$q+=$product['quantity'];
            }

		 $pricedetails =$this->model_sale_poscustomer->getPosProductsTotalPrice( $storemanager_storeID , $posCustomerId );	

			foreach ($pricedetails as $productprice) 
			{
			$totalprice=$productprice['productprice']  ;
			}

        $json['success'] = "  on clicking plus or minus success achieved ";

			$json['total'] = sprintf( $q ."items"  .'-'.$this->currency->format( $totalprice , $this->config->get('config_currency')  ) );


			//$json['total'] = $q;

        }

		}

   $this->response->addHeader('Content-Type: application/json');
   $this->response->setOutput(json_encode($json));


		}

	public function remove() {

		$this->load->language('checkout/cart');

		$json = array();
			
	// Remove

		if (isset($this->request->post['key']))
		{
			$key = $this->request->post['key'];
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($key);

		if ($product_info) 
		{
		
			if (!$json) 
			{

		// $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE product_id = '" . (int)$key . "'   AND session_id = '" . $this->db->escape($this->session->getId()) . "' ");

		$this->load->model('sale/poscustomer');

		$this->load->model('user/user');

		$user_info = $this->model_user_user->getUser($this->user->getId());

	   $storemanager_storeID=$user_info['store_id'];

	   if(isset($this->session->data['TelephoneNumber'])) 
	   { 
		   $poscustomerTelephoneNumber= $this->session->data['TelephoneNumber'];
	   }

		$posrecord= $this->model_sale_poscustomer->getPosCustomerId($poscustomerTelephoneNumber);  

		@$posCustomerId= $posrecord['customer_id'];

		$this->session->data['poscustomerid'] =  $posCustomerId;  

		$data['posCustomerId']=$this->session->data['poscustomerid'];

		$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE product_id = '" . (int)$key . "' and customer_id = '" . (int)$posCustomerId . "'  ");


		//$product_info=$this->model_catalog_product->getProduct($product_id);

		 $poscartrecord3=$this->model_sale_poscustomer->getPosProductsForSingleCart( $storemanager_storeID , $posCustomerId );

	    $json['minimum'] =	$product_info['minimum'];

		 $q=0;

			foreach ($poscartrecord3 as $product) 
			{
			$q+=$product['quantity'];

            }

		 $pricedetails=$this->model_sale_poscustomer->getPosProductsTotalPrice( $storemanager_storeID , $posCustomerId );	

			foreach ($pricedetails as $productprice) 
			{
			$totalprice=$productprice['productprice']  ;
			}

	
	     $json['success'] = "productremoved";

		 $json['total'] = sprintf( $q ."items"  .'-'.$this->currency->format( $totalprice , $this->config->get('config_currency')  ) );

	       }

		}

	       $this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	

	public function info() {
		$this->response->setOutput($this->index());
	}

	}
