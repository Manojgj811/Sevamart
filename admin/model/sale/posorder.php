<?php
class ModelSalePosOrder extends Model {

	public function addOrder($order_data,  $data) {

		//$deleteordestatus=$this->db->query("DELETE FROM oc_order  WHERE  order_status_id =0  ");  
	
      $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET  store_id = '" . (int)$order_data['ordered_storeID'] . "',  customer_id = '" . (int)$order_data['customer_id'] . "' ,store_name = '" . $this->db->escape($order_data['ordered_storeName']) . "', store_url = '" . $this->db->escape($order_data['ordered_storeurl']) . "' ,  customer_group_id = '" . (int)1 . "', 
         firstname = '" . $this->db->escape($order_data['firstname']) . "'  , pickup ='" . (int)1 . "' ,  approvalid = '" . $this->db->escape($order_data['Apporval']) . "'  ,  order_method = '"." POS ". "'  ,   date_added = NOW(), date_modified = NOW(), total = '" . (float)($order_data['total']) . "'  ,  
       order_status_id = '" . (int)5 . "'   ,   payment_method= '". $this->db->escape($order_data['pos']) ."'  ,     telephone = '" . $this->db->escape($order_data['telephone']) . "'  ");
      
      	$order_id = $this->db->getLastId();


     if (isset($data['posproducts'])) {

      foreach ($data['posproducts'] as $product) {
      
       $productStatusId = 7;
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', product_status_id = '" . (int)$productStatusId . "' , name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "',
        quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "',   tax='". (int)$product['taxamount'] . "' ,
         cgstrate='". (int)$product['cgstrate'] . "',  sgstrate='". (int)$product['sgstrate'] . "'  ");
             
        }
                  	
        }

          return $order_id;
      
      
    }

  
  
       public function clearCartProducts($store_id, $customer_id)
   {
             $this->db->query("DELETE FROM oc_cart  WHERE  `store_id` ='". (int)$store_id ."' AND  `customer_id`='" . (int)$customer_id . "'  ");
    
    }



        }
   
        