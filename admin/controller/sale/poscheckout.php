
	<?php
	 
   class ControllerSalePosCheckout extends Controller {

   private $error = array();

   public function index() {

      $this->load->language('sale/order');
      $this->load->model('sale/poscustomer');
      $this->load->model('sale/posorder');
  
       $data['title']='title';

       $this->document->setTitle($this->language->get('heading_title'));

      $data['user_token']=$this->session->data['user_token'];

      $cuserId=$this->user->getId();

      $this->load->model('user/user');

      $user_info = $this->model_user_user->getUser($this->user->getId());

		$storemanager_storeID=$user_info['store_id'];

     $data['stid']= $user_info['store_id'];

     $data['confirm'] = $this->url->link('sale/poscheckout/insert', 'user_token=' . $this->session->data['user_token'], true);

      if(isset($this->session->data['TelephoneNumber'])) 
      { 
            $poscustomerTelephoneNumber= $this->session->data['TelephoneNumber'];

            $posrecord= $this->model_sale_poscustomer->getPosCustomerId($poscustomerTelephoneNumber);  

            $posCustomerId= $posrecord['customer_id'];

         }

   

     // $poscategorypath=$this->session->data['poscategorylink'];

      if( isset($this->session->data['poscategorylink']))
       {
      $data['back']= $this->url->link('product/category', 'user_token=' . $this->session->data['user_token'].'&path='.$this->session->data['poscategorylink'],true);
       }
       
       else
       {
         $data['back']= $this->url->link('sale/pos', 'user_token=' . $this->session->data['user_token'],true);
       }
    
      //echo $posCustomerId;

      $this->session->data['poscustomerid'] =  $posCustomerId;

      $posrecord= $this->model_sale_poscustomer->getPosCustomerId($poscustomerTelephoneNumber); 

      $poscartrecord=$this->model_sale_poscustomer->getPosProductsForSingleCart( $storemanager_storeID , $posCustomerId );

      $this->load->model('catalog/product');

     $product_total=0;

      $cgtax=0;
      $sgtax=0;
      $mysubtotal2=0;

       $totalIncludingGst=0;

     foreach ($poscartrecord as $product) 
     {
          $product_info=$this->model_catalog_product->getProduct($product['product_id']);
           
          $tax= $this->model_sale_poscustomer->getTax($product['product_id']); 

          //  var_dump($tax);  //echo "<br>";
               
                     
         if( $product_info['product_id']==$product['product_id'])
         {
            //echo  $product_info['price'];
         foreach($tax as $taxrateresult2)
         {

         if(strpos(strtoupper($taxrateresult2['taxname']), 'CGST') !== FALSE)
         {
            $gst=$taxrateresult2['Gsttitle'];
            $cgstrate=  $taxrateresult2['taxrate'];  
            $cgstname= $taxrateresult2['taxname'];  

         }

         elseif(strpos(strtoupper($taxrateresult2['taxname']),'SGST') !== FALSE)
         {
         $sgstrate=$taxrateresult2['taxrate'];
         $sgstname= $taxrateresult2['taxname'];  

         //   echo "the sgst is $sgtax";
         }

      }

     // var_dump($gst);

        $unit_price=$product_info['price'];
        $product_total += $product['quantity'];
        $subtotal= $unit_price*$product['quantity'];

       $total = $this->currency->format( $unit_price * $product['quantity'], $this->config->get('config_currency'));

           // $value=  (  $total /100 *  2);

        $amount=$unit_price * $product['quantity'];

           //$rate_sum +=$amount;

     //	$grandtotal+=	$total;

               $data['posproducts'][] = array(
               //newly added product _id key

               'product_id' => $product_info['product_id'],

               'cart_id'   => $product['cart_id'],

               'subtotal'     =>  $subtotal,

               'cgstrate'    => $cgstname,

               'cgstamount'=>   ($cgstrate/ 100)* ($unit_price*$product['quantity']),

              $cgstsum= ($cgstrate/ 100)* ($unit_price*$product['quantity']),

              $sgstsum=  ($sgstrate / 100) *($unit_price*$product['quantity']),

              
					$cgtax+=($cgstrate/ 100)* ($unit_price*$product['quantity']),


					$sgtax+= ($sgstrate / 100) *($unit_price*$product['quantity']),

                 'sgstrate'    => $sgstname,  

              // 'cgstamount'=>   ($cgstrate/ 100)* ($unit_price*$product['quantity']),

              'sgstamount'=>  ($sgstrate / 100) *($unit_price*$product['quantity']),

               //'sgstamount'=>   $sgstrate *$product['quantity'],

                  'name'      => $product_info['name'],

                  'model'     =>  $product_info['model'],
                  
                  'quantity'  => $product['quantity'],

                  'price'     =>  $product_info['price'],

                 $mysubtotal2+=$product_info['price']  * $product['quantity'] ,

                $totalIncludingGst+=    ( ($product_info['price']  * $product['quantity']) + ($cgstsum+$sgstsum) ) ,

      // $this->currency->format(($product['price']  * $product['quantity'])+ (($cgstpAmount+$sgstpAmount)* $product['quantity']) , $this->session->data['currency']),
		
           'total'    =>  $this->currency->format(($product_info['price']* $product['quantity'] )+$cgstsum+$sgstsum , $this->config->get('config_currency'))
            );

            $data['totalamountgrandtotal']=(float)$mysubtotal2;

            $data['cgstgrandtotal']=$cgtax;

            $data['sgstgrandtotal']=$sgtax;

            $data['gstgrandtotal']=$totalIncludingGst;

               }

            }

            

         $data['possubtotal']=array();

         $data['cgstsumsuumary']=array();

         $data['sgstsumsuumary']=array();

         $data['grandtotal']=array();


            $data['possubtotal'][]=array(
            'name'=>'Sub Total(ex Gst)',
            'value'=>  $this->currency->format($mysubtotal2, $this->config->get('config_currency')),   
            );

            $data['cgstsumsuumary'][]=array(
            'name'=>'CGST Amount',
            'value'=>  $this->currency->format( $cgtax , $this->config->get('config_currency')),   
            );


         $data['sgstsumsuumary'][]=array(
         'name'=>'SGST Amount',
         'value'=> $this->currency->format( $sgtax  , $this->config->get('config_currency')),    
         );

         $data['grandtotal'][]=array(
         'name'=>'Grand Total(inc GST ):',
         'value'=> $this->currency->format($totalIncludingGst  , $this->config->get('config_currency')),    
         );

            $data['header']=$this->load->controller('common/header');

            $data['column_left'] = $this->load->controller('common/column_left');

            $data['footer'] = $this->load->controller('common/footer');

            $data['search'] = $this->load->controller('sale/possearch');

            $data['cart'] = $this->load->controller('sale/poscart');

            $data['menu'] = $this->load->controller('sale/posmenu');

            $data['product'] = $this->load->controller('product/category');
  
          
       //   $data['add'] = $this->url->link('sale/pos/addCustomer', 'user_token=' . $this->session->data['user_token'], true);
  
          $this->response->setOutput($this->load->view('sale/poscheckout', $data));
  
         }



   public function insert()
   {

      $order_data=array();

      $this->load->model('sale/posorder');

      $this->load->model('sale/poscustomer');
      $this->load->model('user/user');
      $this->load->model('setting/store');

      $user_info = $this->model_user_user->getUser($this->user->getId());

      $storemanager_storeID=$user_info['store_id'];

      $store_info = $this->model_setting_store->getStore($storemanager_storeID);

       $idcustomer= $this->session->data['poscustomerid'];

       $customer_info=  $this->model_sale_poscustomer->getPosCustomerDetails($storemanager_storeID,$idcustomer);

     
     //var_dump($customer_info);

      // return;

      $order_data['ordered_storeID']=$user_info['store_id'];

      $order_data['customer_id']= $idcustomer;

      $order_data['ordered_storeName']=$store_info['name'];

      $order_data['ordered_storeurl']=$store_info['url'];

      $order_data['firstname']=$customer_info['firstname'];

      $order_data['telephone']=$customer_info['telephone'];

       if(isset($this->session->data['TelephoneNumber'])) 
		{ 
			 $poscustomerTelephoneNumber= $this->session->data['TelephoneNumber'];
      }

        $posrecord= $this->model_sale_poscustomer->getPosCustomerId($poscustomerTelephoneNumber); 

      $poscartrecord=$this->model_sale_poscustomer->getPosProductsForSingleCart( $storemanager_storeID ,  $idcustomer );

      $this->load->model('catalog/product');

      $product_total=0;

      $cgtax=0;

      $sgtax=0;

      $mysubtotal2=0;

       $totalIncludingGst=0;

       $data['posproducts']=array();

    foreach ($poscartrecord as $product) 
      {
           $product_info=$this->model_catalog_product->getProduct($product['product_id']);
           
             $tax= $this->model_sale_poscustomer->getTax($product['product_id']); 

             // var_dump($tax);
              //  echo "<br>";
               
         if( $product_info['product_id']==$product['product_id'])

            {
                  $productname=$product_info['name'];
                  $productmodel=$product_info['model'];

              //echo  $product_info['price'];

                  foreach($tax as $taxrateresult2)
                  {
                     if(strpos(strtoupper($taxrateresult2['taxname']), 'CGST') !== FALSE)
                        {
                           $gst=$taxrateresult2['Gsttitle'];

                           $cgstrate=  $taxrateresult2['taxrate'];  

                           $cgstname= $taxrateresult2['taxname'];  

                        }

                        elseif(strpos(strtoupper($taxrateresult2['taxname']),'SGST') !== FALSE)
                        {
                        
                        $sgstrate=$taxrateresult2['taxrate'];

                        $sgstname= $taxrateresult2['taxname'];  

                        //   echo "the sgst is $sgtax";
                        }

                  }

                  $unit_price=$product_info['price'];

               $product_total += $product['quantity'];

               $subtotal= $unit_price*$product['quantity'];

               $total = $this->currency->format( $unit_price * $product['quantity'], $this->config->get('config_currency'));

               $amount=$unit_price * $product['quantity'];

            //var_dump($product_info);

               //return;

             
               $data['posproducts'][] = array(
               //newly added product _id key

               'product_id' => $product_info['product_id'],

               'name'       => $product_info['name'],

               'model'      => $product_info['model'],

               'cart_id'   => $product['cart_id'],

               'subtotal'     =>  $subtotal,

               'cgstrate'    => $cgstrate,

               'cgstamount'=> ($cgstrate/ 100)* ($unit_price*$product['quantity']),

              $cgstsum= ($cgstrate/ 100)* ($unit_price*$product['quantity']),

              $sgstsum=  ($sgstrate / 100) *($unit_price*$product['quantity']),

              
					$cgtax+=($cgstrate/ 100)* ($unit_price*$product['quantity']),


					$sgtax+= ($sgstrate / 100) *($unit_price*$product['quantity']),

                'sgstrate'    => $sgstrate,  

              // 'cgstamount'=>   ($cgstrate/ 100)* ($unit_price*$product['quantity']),

              'sgstamount'=>  ($sgstrate / 100) *($unit_price*$product['quantity']),

               //'sgstamount'=>   $sgstrate *$product['quantity'],
               // 'name'      => $product_info['name'],

               // 'model'     =>  $product_info['model'],

               'quantity' =>$product['quantity'],

               'price'  =>$product_info['price'],

               'producttotal' =>$product_info['price'],

             $taxcgst=  ($cgstrate/100)*($product_info['price']* $product['quantity'] ),

             $taxsgst=  ($sgstrate/100)*($product_info['price']* $product['quantity'] ),

             $totaltax=$taxcgst+$taxsgst,

            // 'reward'     => $product_info['reward'],

            $mysubtotal2+=$product_info['price']  * $product['quantity'] ,

            $totalIncludingGst+=  ( ($product_info['price']  * $product['quantity']) + ($cgstsum+$sgstsum) ) ,

             'total'    =>   ($product_info['price']* $product['quantity'] ),

            'taxamount'=> $totaltax
           
            );

          //  var_dump($totaltax);

           // return;

           // $order_data['total']  = ($product_info['price']* $product['quantity'] )+$cgstsum+$sgstsum;

            $order_data['total']  =   $totalIncludingGst;

              }

           //   var_dump ($data['posproducts']);

       }

          if (($this->request->server['REQUEST_METHOD'] == 'POST')) 
            {
               if (isset($this->request->post['Apporval']))
               {
               $data['Apporval'] = $this->request->post['Apporval'];

               $order_data['Apporval']=  $data['Apporval'];

               }

               else
               {
               $data['Apporval'] = 0;
               
               }

               if (isset($this->request->post['pos']))
               {
               $data['pos'] = $this->request->post['pos'];

               $order_data['pos']=  $data['pos'];

               }

             }

           $order_id= $this->model_sale_posorder->addOrder($order_data  ,$data);

           $cartTruncateresults=$this->model_sale_posorder->clearCartProducts( $storemanager_storeID ,$idcustomer);

           // unset( $this->session->data['poscustomerid'] );


          $this->response->redirect($this->url->link('sale/posorder/invoice', 'user_token=' . $this->session->data['user_token'].'&order_id='.$order_id , true));

        

         }


      }

  
      
      
      
      
      
      
      
      
      
  