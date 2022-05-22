<?php
class ControllerCheckoutConfirm extends Controller {
	public function index() {
		$redirect = '';

		$totalamountvariable='';

		$this->load->model('setting/store');
	
		$this->load->model('servicearea/servicearea');         
		$this->load->model('account/customer');
		$this->load->model('account/address');

		if ($this->cart->hasShipping()) {


		$shipp=	$this->cart->hasShipping();
		//var_dump($this->session->data['shipping_method']);

			// Validate if shipping address has been set.
			if (!isset($this->session->data['shipping_address'])) {
				//$redirect = $this->url->link('checkout/checkout', '', true);
			}

			// Validate if shipping method has been set.
			if (!isset($this->session->data['shipping_method'])) {
				//$redirect = $this->url->link('checkout/checkout', '', true);
			}
		}
		 else {
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
		}

		// Validate if payment address has been set.
		if (!isset($this->session->data['payment_address'])) {
			$redirect = $this->url->link('checkout/checkout', '', true);
		}

		// Validate if payment method has been set.
		if (!isset($this->session->data['payment_method'])) {
			$redirect = $this->url->link('checkout/checkout', '', true);
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$redirect = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		//var_dump($products);

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$redirect = $this->url->link('checkout/cart');

				break;
		}

	
	}

		if (!$redirect) {
			$order_data = array();

			$totals = array();
			$taxes = $this->cart->getTaxes();
		
		//	var_dump($taxes);
			$total = 0;

		
////////////////////changes to get tax rate name 


			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			$this->load->model('setting/extension');

			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');


			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_' . $value['code']  . '_sort_order');
			}
			//var_dump($value);

           	//////////////////////////////////////
			

           $output='';

			if($this->session->data['shipping_delivery'] == true)
			
			{
				
			$customer_group = $this->model_account_customer->getCustomer($this->customer->getId());
			
			$customerid=$customer_group['firstname'];
		//	echo  "the customer name is   $customerid";
		  //	echo "<br>";
	
			$totalstores = $this->model_setting_store->getStores();
			$customeraddress=$this->model_account_address->getAddresses();
			
		  $idcurrentstore=$this->config->get('config_store_id');
	
		   $pincoderesults =$this->model_servicearea_servicearea->getPin($idcurrentstore);
	
		   $chargeshipping='';

			foreach ($pincoderesults as $result)
			{
				$pincodevalue=$result['pincode_no'];  

             	$chargeshipping=$result['delivery_charges'];

			  foreach($customeraddress as $resultaddress)
			{
				$addresspostcode=$resultaddress['postcode'];	
			}
				
			if ($addresspostcode==$pincodevalue)
		    { 
				//echo "ssexceuted";
			  $output=$chargeshipping;

			  $data['deliverydetailtotals'][] = array(
				'title'=>'Shipping Charges(ex GST)',
				'text' =>  $output
				
			);

		    }
	
			}

	

		}
		//	echo "the delivery charge amount is  $output";
	
		//	echo "<br>";

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);

					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
/////////////////////////////////changes made

			
           }

			//var_dump($result['code']);

			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);

			$order_data['totals'] = $totals;

			//var_dump($order_data['totals'] );
			
			$this->load->language('checkout/checkout');

			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');

			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				if ($this->request->server['HTTPS']) {
					$order_data['store_url'] = HTTPS_SERVER;
				} else {
					$order_data['store_url'] = HTTP_SERVER;
				}
			}
			
			$this->load->model('account/customer');

			if ($this->customer->isLogged()) 
			{
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

				$order_data['customer_id'] = $this->customer->getId();
				$order_data['customer_group_id'] = $customer_info['customer_group_id'];
				$order_data['firstname'] = $customer_info['firstname'];
				$order_data['lastname'] = $customer_info['lastname'];
				$order_data['email'] = $customer_info['email'];
				$order_data['telephone'] = $customer_info['telephone'];
				$order_data['custom_field'] = json_decode($customer_info['custom_field'], true);
			} 
			elseif (isset($this->session->data['guest'])) 
			{
				$order_data['customer_id'] = 0;
				$order_data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
				$order_data['firstname'] = $this->session->data['guest']['firstname'];
				$order_data['lastname'] = $this->session->data['guest']['lastname'];
				$order_data['email'] = $this->session->data['guest']['email'];
				$order_data['telephone'] = $this->session->data['guest']['telephone'];
				$order_data['custom_field'] = $this->session->data['guest']['custom_field'];
			}

			$order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
			$order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
			$order_data['payment_company'] = $this->session->data['payment_address']['company'];
			$order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
			$order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
			$order_data['payment_city'] = $this->session->data['payment_address']['city'];
			$order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
			$order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
			$order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
			$order_data['payment_country'] = $this->session->data['payment_address']['country'];
			$order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
			$order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
			$order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

			if (isset($this->session->data['payment_method']['title'])) {
				$order_data['payment_method'] = $this->session->data['payment_method']['title'];
			} else {
				$order_data['payment_method'] = '';
			}

			if (isset($this->session->data['payment_method']['code'])) {
				$order_data['payment_code'] = $this->session->data['payment_method']['code'];
			} else {
				$order_data['payment_code'] = '';
			}

			if ( $this->cart->hasShipping()  &&  $this->session->data['shipping_delivery'] == true   ) {

			
			//	echo "yeah shipping is   there  for prduct ";

				$order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
				
             $order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
				
				//var_dump($order_data['shipping_lastname']);

				$order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
				$order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];

               	$order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];

				$order_data['shipping_city'] = $this->session->data['shipping_address']['city'];

				

				$order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];

				$order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];

			$order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];

        	$order_data['shipping_country'] = $this->session->data['shipping_address']['country'];

			

				$order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
				$order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
				$order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

				if (isset($this->session->data['shipping_method']['title'])) 
				{
					$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];

				 	}
		 
				else
				{
					  $order_data['shipping_method'] = '';

				}

				if (isset($this->session->data['shipping_method']['code'])) {
					$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
				} else {
					$order_data['shipping_code'] = '';
				}
			} 
			
			
			else {
			
			
			//	echo "yeah shipping is not  there";


				$order_data['shipping_firstname'] = '';
				$order_data['shipping_lastname'] = '';
				$order_data['shipping_company'] = '';
				$order_data['shipping_address_1'] = '';
				$order_data['shipping_address_2'] = '';
				$order_data['shipping_city'] = '';
				$order_data['shipping_postcode'] = '';
				$order_data['shipping_zone'] = '';
				$order_data['shipping_zone_id'] = '';
				$order_data['shipping_country'] = '';
				$order_data['shipping_country_id'] = '';
				$order_data['shipping_address_format'] = '';
				$order_data['shipping_custom_field'] = array();
				$order_data['shipping_method'] = '';
				$order_data['shipping_code'] = '';
			}

			$order_data['products'] = array();

			$cgstcommissionamount=0;
			$sgstcommissionamount=0;

        	foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				/////////////////////////
				$cgst='';
				$sgst='';
			    $cgstAmount='';
			    $sgstAmount='';

			$getrates=$this->tax->getRates($product['price'], $product['tax_class_id']);

			if(count($getrates) > 0){
				foreach($getrates as $taxrateresult)
				{
					if(strpos(strtoupper($taxrateresult['name']),'CGST') !== FALSE)
					{
					   $cgst=$taxrateresult['name'];
					
					   $cgstAmount=$taxrateresult['amount'];

					   $cgstcommissionamount+=($taxrateresult['amount'] * $product['quantity']);

					$outputString = preg_replace('/[^0-9]/', '',$cgst);  

						//echo "";

					//echo "the nos is $outputString";

			        $cgstrate=substr($taxrateresult['name'],5,1);

		           }  
	
			elseif(strpos(strtoupper($taxrateresult['name']), 'SGST') !== FALSE)
			{
				$sgst=$taxrateresult['name']; 
				$sgstAmount=$taxrateresult['amount'];
				
				$sgstcommissionamount+=($taxrateresult['amount']* $product['quantity']);

						$sgstrate=substr($taxrateresult['name'],5,2);
        	$outputString2 = preg_replace('/[^0-9]/', '',$sgst);  

			}
			$taxamount= $taxrateresult['amount'];

	         }
	   }

		else{
                $outputString = "";
				$outputString2 = "";
			} 

						foreach ($product['option'] as $option) {
							$option_data[] = array(
								'product_option_id'       => $option['product_option_id'],
								'product_option_value_id' => $option['product_option_value_id'],
								'option_id'               => $option['option_id'],
								'option_value_id'         => $option['option_value_id'],
								'name'                    => $option['name'],
								'value'                   => $option['value'],
								'type'                    => $option['type']
							);
						}


						$order_data['products'][] = array(
							'product_id' => $product['product_id'],
							'name'       => $product['name'],
							'model'      => $product['model'],
							'option'     => $option_data,
							'download'   => $product['download'],
							'quantity'   => $product['quantity'],
							'subtract'   => $product['subtract'],
							'price'      => $product['price'],
							'total'      => $product['total'],
							'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id'])*$product['quantity'],
							
							'cgstrate'=>  $outputString,
							'sgstrate'=>  $outputString2,   
						//	 'deliverycharge'=>$output,
		////////////newly created productamount key
							//'productamount'=>$product['total']+$output,
							'reward'     => $product['reward']
			          	);

              	 $ssss=$this->tax->getTax($product['price'], $product['tax_class_id']);

			
				$bik= $product['price'];
				
				            /////// to order tax table
	        }

              /////////// var_dump($product['total']);

			// Gift Voucher
			$order_data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$order_data['vouchers'][] = array(
						'description'      => $voucher['description'],
						'code'             => token(10),
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'amount'           => $voucher['amount']
					);
				}
			}

			$order_data['comment'] = $this->session->data['comment'];
			
///////////////////////////// to inculde gst commission percentage for delivery shipping charge to total amount 

        
		   $this->load->model('checkout/order');

		   $deliverycgsttaxrate='';
		   $deliverysgsttaxrate='';
		   $cgstdeliverycommissionamount='';
		   $sgstdeliverycommissionamount='';

		   if($this->session->data['shipping_delivery'] == true )
		   {

		   $gsttaxdeliverypincode =$this->model_servicearea_servicearea->getPin($idcurrentstore);
		
		     foreach ($gsttaxdeliverypincode as $result)
		   {
			   $pincodevalue=$result['pincode_no']; 
			   $chargeshipping=$result['delivery_charges'];
			   $taxvalueID=$result['tax_class_id'];

			  // echo  "the service area pincodes are $pincodevalue";

			   /// customer pincode
			   foreach($customeraddress as $resultaddress)
			   {
				   $addresspostcode=$resultaddress['postcode'];
			   }

			 //  echo "<br>";
			   if ($addresspostcode==$pincodevalue)
			   { 
				   //echo "ssexceuted";
				 $taxoutput=$taxvalueID;
			   //}

			   $deliveryTaxRates3=$this->model_checkout_order->deliveryTaxCommissionBasedonpincode($idcurrentstore, $taxoutput);

              foreach($deliveryTaxRates3  as  $deliverytaxratevalue )
			      { 
				   if(strpos(strtoupper($deliverytaxratevalue['name']), 'CGST') !== FALSE)
				   {
					   $deliverycgsttaxname= $deliverytaxratevalue['name'];
					   $deliverycgsttaxrate= $deliverytaxratevalue['rate'];
					   
					}
				   elseif(strpos(strtoupper($deliverytaxratevalue['name']), 'SGST') !== FALSE)
				   {
					   $deliverysgsttaxname= $deliverytaxratevalue['name'];
					   $deliverysgsttaxrate= $deliverytaxratevalue['rate'];
					   
					}
			   }

		   }

		   }

		    $cgstdeliverycommissionamount=($deliverycgsttaxrate/100)*$output;
           $sgstdeliverycommissionamount=($deliverysgsttaxrate/100)*$output;


		   $order_data['total'] = $total_data['total']+$output+$cgstdeliverycommissionamount+$sgstdeliverycommissionamount;

		   $res=$order_data['total'];

		//    echo " shipping delivery  the cost is $res";

		}


		////// if pick up is selected then no need to add delivery charge and delivery tax rates to total order amount 
		if(  $this->session->data['shipping_pickup'] == true )
		{
			$order_data['total'] = $total_data['total'];

			$res=$order_data['total'];

			// echo " pick up from store the cost is $res";
		}

  

	//	echo "the duddu is $res";

			//$totalamountvariable2=$this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']+$output , $this->session->data['currency']);

        	if (isset($this->request->cookie['tracking'])) 
				{
				$order_data['tracking'] = $this->request->cookie['tracking'];

				$subtotal = $this->cart->getSubTotal();

				//

				// Affiliate
				$affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

				if ($affiliate_info) {
					$order_data['affiliate_id'] = $affiliate_info['customer_id'];
					$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
				}

				// Marketing
				$this->load->model('checkout/marketing');

				$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

				if ($marketing_info) {
					$order_data['marketing_id'] = $marketing_info['marketing_id'];
				} else {
					$order_data['marketing_id'] = 0;
				}
			} else {
				$order_data['affiliate_id'] = 0;
				$order_data['commission'] = 0;
				$order_data['marketing_id'] = 0;
				$order_data['tracking'] = '';
			}

			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
			$order_data['currency_code'] = $this->session->data['currency'];
			$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$order_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$order_data['accept_language'] = '';
			}

            ////////////////  select the payment based on radio button selection in checkout
			
			if (isset($this->session->data['payment_method']['code'])) {
				$data['code'] = $this->session->data['payment_method']['code'];
				//var_dump(	$data['code'] );
			}
	
		$this->load->model('storecontract/storecontract');
	
			$totalstores2 = $this->model_setting_store->getStores();
	
			// foreach ($totalstores2  as $result) 
			// {
	
		$idcurrentstore=$this->config->get('config_store_id');
	
		$storesubscribe=$this->model_storecontract_storecontract->getStoresSubscription2($idcurrentstore);
	
		     if($data['code']=='cod')
			{
				foreach($storesubscribe  as $storesubresult)
					{
				$data['subscribepayment'][]=array(
					'cashpayment'=>$storesubresult['commission_percentage_cod'],
						
						);

			@$GSTCommissionPercentage=$storesubresult['tax_class_id'];		
			@$az=$storesubresult['commission_percentage_cod'];		
	
					}
			}
	
			if($data['code']=='razorpay')
			{
			foreach($storesubscribe  as $storesubresult2)
				{
			$data['subscribepayment'][]=array(
				'onlinepayment'=>$storesubresult2['commission_percentage_card'],
					
				);

			@$bz=$storesubresult2['commission_percentage_card'];		
	
				   }
			}
	
		  // }
	
		$cash=@$storesubresult['commission_percentage_cod'] ;
	//	echo  "the  cash commision is $cash";
	
	//	echo "<br>";
		//echo  "the  gst   commision is $GSTCommissionPercentage";


	//	echo "<br>";

	//	echo  @$az;
	//	echo "<br>";
	//	echo  @$bz;

		$online=@$storesubresult2['commission_percentage_card'] ;
	
	//	echo  "the online commision is $online";			
	
		$comcash=$cash;
		$comonline=	$online;
	
		// $cashpercentcommission=$comcash;
		// $onlinepercentcommission=$comonline;

		@$percentcashcodcommission=$comcash;
		@$percentonlinerazorpaycommission=$comonline;
	
		// echo "the selected percentcashcodcommission percentage is $percentcashcodcommission";
	 	// echo "<br>";
		// echo "the selected percentonlinerazorpaycommission percentage is $percentonlinerazorpaycommission";
	
            	$subtotalresults='';
			///////////////for sub total created by Sharath

			if($this->session->data['shipping_pickup'] == true )
			{
			foreach ($order_data['totals'] as $total)
			{
			if($total['code'] == 'sub_total' )
			{
				$data['subtotal'][]=array(
					'name'=>'Sub Total(ex Gst)',
						'value'=>$total['value']
					);
					$subtotalresults=$total['value'];
			}
		
			}
			//echo "the subtotal of commission report is $subtotalresults ";
		}
			//

			$commissioncashorderamount='';
			if($this->session->data['shipping_delivery'] == true )
		   {

			foreach ($order_data['totals'] as $total)
			{
			if($total['code'] == 'sub_total' )
			{
				$data['subtotal'][]=array(
					'name'=>'Sub Total(ex Gst)',
						'value'=>$total['value']+$output
					);
					$subtotalresults=$total['value'];
			}
		
			}
		}
		@$commissioncashorderamount=($percentcashcodcommission/ 100)*$subtotalresults ;

		@$commissiononlineorderamount=($percentonlinerazorpaycommission/ 100)* $subtotalresults;
		   
	//	echo "the obtained cash  commission amount is $commissioncashorderamount";
      //  echo "<br>";
	//	echo "the obtained commission amount is $commissionorderamount2";
		//echo "the obtained  online commission amount is $commissiononlineorderamount";
       
		//echo "<br>";
     //echo "the total cgst  commission amount is $cgstcommissionamount";
		//echo "<br>";
		
		//echo "the total sgst  commission amount is $sgstcommissionamount";
       
       	//////////////////////// for order tax 

 /////////////////////////**************************** */
 
				
		if($this->session->data['shipping_delivery'] == true )
		{
		// echo "pick up  customer ";
		$gsttaxdeliverypincode =$this->model_servicearea_servicearea->getPin($idcurrentstore);

	//	echo "<br>";

		foreach ($gsttaxdeliverypincode as $result)
		{
			$pincodevalue=$result['pincode_no']; 
			$chargeshipping=$result['delivery_charges'];
			$taxvalueID=$result['tax_class_id'];

	 /// customer pincode
				foreach($customeraddress as $resultaddress)
				{
					$addresspostcode=$resultaddress['postcode'];
				}

		if ($addresspostcode==$pincodevalue)
			{ 
				// echo "ssexceuted";
			$taxoutput=$taxvalueID;
			//}
		$deliveryTaxRates3=$this->model_checkout_order->deliveryTaxCommissionBasedonpincode($idcurrentstore, $taxoutput);


			foreach($deliveryTaxRates3  as  $deliverytaxratevalue )
			{ 
				if(strpos(strtoupper($deliverytaxratevalue['name']), 'CGST') !== FALSE)
				{
					$deliverycgsttaxname= $deliverytaxratevalue['name'];
					$deliverycgsttaxrate= $deliverytaxratevalue['rate'];
				

				}
				elseif(strpos(strtoupper($deliverytaxratevalue['name']), 'SGST') !== FALSE)
				{
					$deliverysgsttaxname= $deliverytaxratevalue['name'];
					$deliverysgsttaxrate= $deliverytaxratevalue['rate'];
					
				}
			}

			}

		}

		$cgstdeliverycommissionamount=($deliverycgsttaxrate/100)*$output;
		$sgstdeliverycommissionamount=($deliverysgsttaxrate/100)*$output;

	}



		//    $deliveryTaxRates=$this->model_checkout_order->deliveryTaxCommission($idcurrentstore);

   	$this->load->model('checkout/order');
		   
	$commissiontaxrates=$this->model_checkout_order->taxcommission($idcurrentstore);

		//  var_dump($commissiontaxrates);
		// // echo "<br>";

		foreach($commissiontaxrates  as  $taxratevalue )
		{ 
			if(strpos(strtoupper($taxratevalue['name']), 'CGST') !== FALSE)
			{
				$cgsttaxname= $taxratevalue['name'];
				$cgsttaxrate= $taxratevalue['rate'];
				
				// echo "the cgsttax name is $cgsttaxname ";
				
				// echo "the cgsttax rate is $cgsttaxrate";
			
            }
			elseif(strpos(strtoupper($taxratevalue['name']), 'SGST') !== FALSE)
			{
				$sgsttaxname= $taxratevalue['name'];
				$sgsttaxrate= $taxratevalue['rate'];
				
         	}
	    }

		$cgsttaxcommissionamountcash=($cgsttaxrate/100)*$commissioncashorderamount;

		
		$sgsttaxcommissionamountcash=($sgsttaxrate/100)*$commissioncashorderamount;
		// echo "the obtained  sgsttaxcommissionamount   is $sgsttaxcommissionamountcash ";
		// echo "<br>";
	$total_commission_amountcod=$sgsttaxcommissionamountcash+$cgsttaxcommissionamountcash+$commissioncashorderamount;
		
   
			if($data['code']=='cod')
			{
				$order_data['commissionproducts'][] = array(
					
		////////////newly created productamount key
			'delivery_charge'=>$output,
			'cgst_order_amount'=>$cgstcommissionamount,
			'sgst_order_amount'=>$sgstcommissionamount, 
			'sub_total'  => $subtotalresults,
			'cgst_delivery_percentage'=>$deliverycgsttaxrate,
			'cgst_delivery_amount'=>$cgstdeliverycommissionamount,
			'sgst_delivery_percentage'=>$deliverysgsttaxrate,
			'sgst_delivery_amount'=>$sgstdeliverycommissionamount,

			'commission_percentage'=>$az,
			
			'commission_amount'=> $commissioncashorderamount,
			
			// 'commission_amount'=>$commissionorderamount2,
			'cgst_commission_percentage'=>$cgsttaxrate,
			'cgst_commission_amount'=>$cgsttaxcommissionamountcash,
			'sgst_commission_percentage'=>$sgsttaxrate,
			'sgst_commission_amount'=>$sgsttaxcommissionamountcash,
			'total_commission_amount'=>$total_commission_amountcod			 

			 );
		
			}
		
			////////
		$cgsttaxcommissionamountcard=($cgsttaxrate/100)*$commissiononlineorderamount;

	//	echo "the obtained  cgsttaxcommissionamountcard   is $cgsttaxcommissionamountcard ";

		$sgsttaxcommissionamountcard=($sgsttaxrate/100)*$commissiononlineorderamount;
	//	echo "the obtained  sgsttaxcommissionamountcard   is $sgsttaxcommissionamountcard ";
	//	echo "<br>";

		$total_commission_amountonline=$sgsttaxcommissionamountcard+$cgsttaxcommissionamountcard+$commissiononlineorderamount;


			if($data['code']=='razorpay')
				{
				$order_data['commissionproducts'][] = array(
					
			   'delivery_charge'=>$output,
			   'cgst_order_amount'=>$cgstcommissionamount,
			   'sgst_order_amount'=>$sgstcommissionamount, 
				  'sub_total'  => $subtotalresults,
			   'cgst_delivery_percentage'=>$deliverycgsttaxrate,
			   'cgst_delivery_amount'=>$cgstdeliverycommissionamount,
			   'sgst_delivery_percentage'=>$deliverysgsttaxrate,
			   'sgst_delivery_amount'=>$sgstdeliverycommissionamount,
			   'commission_percentage'=>$bz,
			   //'commission_percentage'=>$onlinepercentcommission,
				'commission_amount'=> $commissiononlineorderamount,
			 
			  // 'commission_amount'=>$commissionorderamount2,
			   'cgst_commission_percentage'=>$cgsttaxrate,
			   'cgst_commission_amount'=>$cgsttaxcommissionamountcard,
			  'sgst_commission_percentage'=>$sgsttaxrate,
			  'sgst_commission_amount'=>$sgsttaxcommissionamountcard,
			  'total_commission_amount'=>$total_commission_amountonline			 
	
				 );
				}
	
       	// echo "the subtotal of whole product is $subtotalresults";

		// echo "<br>";
		// echo "the cgstcommissionamount of whole product is $cgstcommissionamount";
		
		// echo "the sgstcommissionamount of whole product is $sgstcommissionamount";
			
		    $this->load->model('checkout/order');

			/// to remove unwanted order records /missed order records from database table developed/done  by sharath 
			// $myquery=$this->db->query("DELETE oc_order_product FROM oc_order LEFT JOIN oc_order_product ON oc_order.order_id=oc_order_product.order_id 
			// WHERE oc_order.order_status_id =0 ");

			// $myquery2=$this->db->query("DELETE oc_order_tax FROM oc_order LEFT JOIN oc_order_tax ON oc_order.order_id=oc_order_tax.order_id 
			// WHERE oc_order.order_status_id =0 ");
		
			// $myquery3=$this->db->query("DELETE oc_order_total FROM oc_order LEFT JOIN oc_order_total ON oc_order.order_id=oc_order_total.order_id 
			// WHERE oc_order.order_status_id =0 ");


        $order_data['shipping_type'] = $this->session->data['shipping_pickup'];

		// var_dump($order_data['shipping_type']);
		// echo "<br>";

		$order_data['shipping_type_delivery'] = $this->session->data['shipping_delivery'];

		// var_dump($order_data['shipping_type_delivery']);


			$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

			//var_dump($this->session->data['order_id'] );
		////////////////////////////////////////////////////////////		

			$this->load->model('tool/upload');

			$data['products'] = array();

				$cgtax=0;
				$sgtax=0;
				$mysubtotal2=0;
				$mytotalincgst2=0;
			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				$subres=0;
				$subres+=$product['price']  * $product['quantity'];

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				$recurring = '';

				if ($product['recurring']) {
					$frequencies = array(
						'day'        => $this->language->get('text_day'),
						'week'       => $this->language->get('text_week'),
						'semi_month' => $this->language->get('text_semi_month'),
						'month'      => $this->language->get('text_month'),
						'year'       => $this->language->get('text_year'),
					);

					if ($product['recurring']['trial']) {
						$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
					}

					if ($product['recurring']['duration']) {
						$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					} else {
						$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					}
				}

					$cgstp='';
					$sgstp='';
					$cgstpAmount='';
					$sgstpAmount='';
				
					$cgstcommissionamount2=0;
				
					$sgstcommissionamount2=0;

	 $getrates2=$this->tax->getRates($product['price'], $product['tax_class_id']);
		
	 
	 //var_dump(count( $getrates2));
       foreach($getrates2 as $taxrateresult2)
	  {
				if(strpos(strtoupper($taxrateresult2['name']), 'CGST') !== FALSE)
				 {
					$cgstp =$taxrateresult2['name'];

					
					$cgstpAmount=$taxrateresult2['amount'];
					$cgstcommissionamount2+=$taxrateresult2['amount'];

					$outputString = preg_replace('/[^0-9]/', '', $cgstp); 

					//	echo "the nos is $outputString";

					//     echo "the cgst is $cgtax";
				 }  
		
				 elseif(strpos(strtoupper($taxrateresult2['name']),'SGST') !== FALSE)
		         {
	     	  $sgstp=$taxrateresult2['name']; 

			
			  $sgstpAmount=$taxrateresult2['amount'];
			  
			  $sgstcommissionamount2+=$taxrateresult2['amount'];

           //   echo "the sgst is $sgtax";
		         }
              //	$taxamount= $taxrateresult['amount'];

		}

                $data['products'][] = array(
					'cart_id'    => $product['cart_id'],
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'recurring'  => $recurring,
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					///////////////////////created newly
					'taxclass'   => $product['tax_class_id'],
					'delivery'=>$output,
					//'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
					'prices'      => $this->currency->format($product['price'],$this->session->data['currency']),
					'cgstrate'       =>$cgstp,
					'sgstrate'      =>$sgstp,

					'cgstamount'=>$cgstpAmount*$product['quantity'],
					'sgstamount'=>$sgstpAmount*$product['quantity'],

					$cgtax+=$cgstpAmount*$product['quantity'],

					$sgtax+=$sgstpAmount*$product['quantity'],
 
					'subtotal'   => $this->currency->format($product['price']  * $product['quantity'] , $this->session->data['currency']),
					$mysubtotal=$this->currency->format($product['price']  * $product['quantity'] , $this->session->data['currency']),
				
					$mysubtotal2+=$product['price']  * $product['quantity'] ,
					//'totalgst'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'] +$cgtax+$sgtax, $this->session->data['currency']),
				
					$mytotalincgst2+=(($product['price']  * $product['quantity'])+ (($cgstpAmount+$sgstpAmount)* $product['quantity']) ),

					'total'      => $this->currency->format(($product['price']  * $product['quantity'])+ (($cgstpAmount+$sgstpAmount)* $product['quantity']) , $this->session->data['currency']),

					'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
								
				);
			
				$data['totalamountgrandtotal']=(float)$mysubtotal2;

				$data['cgstgrandtotal']=$cgtax;
		
				$data['sgstgrandtotal']=$sgtax;
				
				$data['gstgrandtotal']=$mytotalincgst2;	

             	$totalamountvariable=$this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'] , $this->session->data['currency']);
		//	echo "the subres is $subres";
          }
		// Gift Voucher
			$data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$data['vouchers'][] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency'])
					);
				}
			}

			///////////////////////////////////////////////////////////////
			
	        //echo "<br>";
         	$data['totals'] = array();
			 $answer=0;

			foreach ($order_data['totals'] as $total)
			 {
				if($total['code'] != 'shipping' &&  $total['code'] !='sub_total' )
				{
					$bd=$total['value'];
					$answer=$bd;
						$data['totals'][] = array(
					'title' => $total['title'].'amount',
					'text'  => $this->currency->format($total['value'], $this->session->data['currency']),
					'totalproductamount'=>	$totalamountvariable
				);

             $vvvvv= $total['title'];
               	}
		     }


            
		 //   $deliveryTaxRates3=$this->model_checkout_order->deliveryTaxCommission($idcurrentstore);

	
		 $cgstdeliverycommissionamount=0;
		 $sgstdeliverycommissionamount=0;

		 if($this->session->data['shipping_delivery'] == true)
		 {

		    $gsttaxdeliverypincode =$this->model_servicearea_servicearea->getPin($idcurrentstore);
		
		   // var_dump($gsttaxdeliverypincode);

			foreach ($gsttaxdeliverypincode as $result)
			{
				$pincodevalue=$result['pincode_no']; 
				$chargeshipping=$result['delivery_charges'];
				$taxvalueID=$result['tax_class_id'];

					/// customer pincode
				foreach($customeraddress as $resultaddress)
				{
					$addresspostcode=$resultaddress['postcode'];
				}

				if ($addresspostcode==$pincodevalue)
				{ 
				//	echo "ssexceuted";
				  $taxoutput=$taxvalueID;
				//}

			
				$deliveryTaxRates3=$this->model_checkout_order->deliveryTaxCommissionBasedonpincode($idcurrentstore, $taxoutput);


				foreach($deliveryTaxRates3  as  $deliverytaxratevalue )
				{ 
					if(strpos(strtoupper($deliverytaxratevalue['name']), 'CGST') !== FALSE)
					{
						$deliverycgsttaxname= $deliverytaxratevalue['name'];
						$deliverycgsttaxrate= $deliverytaxratevalue['rate'];
						
					}
					elseif(strpos(strtoupper($deliverytaxratevalue['name']), 'SGST') !== FALSE)
					{
						$deliverysgsttaxname= $deliverytaxratevalue['name'];
						$deliverysgsttaxrate= $deliverytaxratevalue['rate'];
						
					 //    echo "the sgsttax rate is $deliverysgsttaxname";
					 //    echo "<br>";
					 }
				}

			}

			}
  
			//echo $taxoutput;

           $cgstdeliverycommissionamount=($deliverycgsttaxrate/100)*$output;
           $sgstdeliverycommissionamount=($deliverysgsttaxrate/100)*$output;



		   $data['cgstdeliverytotalsum'][]=array(
			'title'=>'CGST Amount on shipping(@'.$deliverycgsttaxrate .'%):',
			'cgstvalue'=> $this->currency->format(  $cgstdeliverycommissionamount,$this->session->data['currency'])
				 );

			$data['sgstdeliverytotalsum'][]=array(
			'title'=>'SGST Amount on shipping(@'.$deliverysgsttaxrate .'%):',
			'sgstvalue'=> $this->currency->format(  $sgstdeliverycommissionamount,$this->session->data['currency'])
					);
		
			
		}

		//////////for total product cgst amount 
				$data['cgsttotalsum'][]=array(
					'title'=>'CGST Amount',
					'cgstvalue'=> $this->currency->format($cgtax+$cgstdeliverycommissionamount,$this->session->data['currency'])
						 );
	
						
				   //////////for total product sgst amount 
	
				$data['sgsttotalsum'][]=array(
					'title'=>'SGST Amount',
					'sgstvalue'=> $this->currency->format($sgtax+$sgstdeliverycommissionamount,$this->session->data['currency'])
						 );
  

				
	/////////////////////for grandtotal	
		$data['totalchargeamount'][] = array(
			'titleamount'=>'Grand Total(inc GST )',
			'bill' => $this->currency->format( $order_data['total'], $this->session->data['currency'] )
			
		);
	/////////////////////////////////////////////////////////
	$data['payment'] = $this->load->controller('extension/payment/' . $this->session->data['payment_method']['code']);
		} 
		else
		 {
			$data['redirect'] = $redirect;
		}

		$this->response->setOutput($this->load->view('checkout/confirm', $data));
	}
}
