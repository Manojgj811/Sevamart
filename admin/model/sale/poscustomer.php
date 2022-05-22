
<?php
	class ModelSalePosCustomer extends Model
	 {
		public function addCustomer($data, $storemanager_storeID) 
			{
			$customer_info = $this->getPosCustomerByPhone($data['Number']);

			if($customer_info)
			{
               $customer_id = $customer_info['customer_id'];
			}
			else
			{

			$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['customername']) . "',   telephone = '" . $this->db->escape($data['Number']) . "', customer_group_id ='".(1)."', store_id = '" . $this->db->escape($storemanager_storeID) . "', status = 1 ,date_added = NOW()");

			$customer_id = $this->db->getLastId();

			$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id .  "', firstname = '" . $this->db->escape($data['customername']) . "' ");
		
            }

			}

		public function getPosCustomerId($PosCustomerId) 
		{
			$query = $this->db->query("SELECT `customer_id` FROM `oc_customer` WHERE telephone= '".$PosCustomerId."' ");

			return $query->row;

		}

		public function getPosCustomerDetails($posstore_id,$PosCustomerId) 
		{
			$query =$this->db->query("SELECT * FROM `oc_customer` WHERE `store_id`='" . (int)$posstore_id . "' AND  `customer_id`='" . (int)$PosCustomerId . "'  ");

			return $query->row;

		}

		public function getPosProductsForSingleCart($posstore_id,$PosCustomerId) 
		{
			$query =$this->db->query("SELECT * FROM `oc_cart` WHERE `store_id`='" . (int)$posstore_id . "' AND  `customer_id`='" . (int)$PosCustomerId . "'  ");

			return $query->rows;

		}

          
       public function getPosProductsTotalPrice($posstore_id,$PosCustomerId) 
		{
	       $query =$this->db->query("SELECT cart.product_id as cartproductId , cart.quantity as qty, product.product_id as productID , SUM(product.price * cart.quantity) as productprice 
	       FROM oc_cart cart LEFT JOIN oc_product product ON cart.product_id=product.product_id 
		    where cart.store_id='" . (int)$posstore_id . "' and cart.customer_id='" . (int)$PosCustomerId . "' ");
	
	     return $query->rows;
        }

         public function getPosCustomerByPhone($telephone) 
	    {
		$query = $this->db->query("SELECT * FROM `oc_customer` WHERE telephone= '". $this->db->escape($telephone)."' ");

		return $query->row;

	    }

      	public function getTax($productID) 
		{
		//$query =$this->db->query(" SELECT op.product_id as productid, octax.title as Gsttitle, op.price , octax.tax_class_id FROM oc_product op LEFT join oc_tax_class octax on octax.tax_class_id = op.tax_class_id where product_id=$productID ");

		$query =$this->db->query("SELECT op.product_id as productid,taxrate.name as taxname,taxrate.rate as taxrate, taxrule.tax_rule_id,taxrule.tax_class_id, octax.title as Gsttitle, op.price , octax.tax_class_id 
		FROM oc_product op LEFT join oc_tax_class octax on octax.tax_class_id = op.tax_class_id LEFT JOIN oc_tax_rule taxrule
		ON taxrule.tax_class_id= op.tax_class_id LEFT JOIN oc_tax_rate taxrate on taxrate.tax_rate_id=taxrule.tax_rate_id where product_id=$productID	");	
		
		return $query->rows;

		}

       public function clearCartProducts($store_id, $customer_id)
	   {
       $this->db->query("DELETE FROM oc_cart  WHERE  `store_id` ='". (int)$store_id ."' AND  `customer_id`='" . (int)$customer_id . "'  ");

	  }

    }
