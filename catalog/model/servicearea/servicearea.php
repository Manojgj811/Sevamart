<?php
class ModelServiceareaServicearea extends Model {

  	public function getPin($storeid) {
		$filter_data = $this->cache->get('servicearea');
		
      //  var_dump($storeid);
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "servicearea WHERE store_id = " . $storeid );

		$filter_data = $query->rows;
			//$this->cache->set('servicearea', $filter_data);
		//var_dump($filter_data);
		return $filter_data;

	}

	public function getPin1($whishresults) {
		$filter_data = $this->cache->get('servicearea');
		
      //  var_dump($storeid);
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "servicearea WHERE store_id = " .  $whishresults );

		$filter_data = $query->rows;
			//$this->cache->set('servicearea', $filter_data);
		//var_dump($filter_data);
		return $filter_data;

	}

	
   public function getPincodedeliverycharge($storeid) {
		$filter_data = $this->cache->get('servicearea');
		
      //  var_dump($storeid);
		
			$query = $this->db->query("SELECT delivery_charges FROM " . DB_PREFIX . "servicearea WHERE store_id = " . $storeid );

			$filter_data = $query->rows;
			//$this->cache->set('servicearea', $filter_data);
		//var_dump($filter_data);
		return $filter_data;

	}

	

	////////////////////created by sharath for delivery charge
	public function getGSTDeliveryCharge($storeid) {
		$filter_data = $this->cache->get('servicearea');
		
      //  var_dump($storeid);
		
			$query = $this->db->query("SELECT tax_class_id FROM " . DB_PREFIX . "servicearea WHERE store_id = " . $storeid );

			$filter_data = $query->rows;
			//$this->cache->set('servicearea', $filter_data);
		//var_dump($filter_data);
		return $filter_data;

	}



	public function getsearchPin() {
		$filter_data = $this->cache->get('servicearea');
		
      //  var_dump($storeid);
		
			$query = $this->db->query("SELECT Pincode_no FROM " . DB_PREFIX . "servicearea" );

			$filter_data = $query->rows;
			//$this->cache->set('servicearea', $filter_data);
		//var_dump($filter_data);
		return $filter_data;

	}
	
	
	////////////////////created by vidya for total service area
	public function getTotalPin() {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "servicearea");

			return $query->row['total'];
		}
   
	}



 


















