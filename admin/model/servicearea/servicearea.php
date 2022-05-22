<?php
class ModelServiceareaServicearea extends Model {

  public function addPin($data,$storeid)
  {
    if (isset($data['servicearea'])) 
  	{
		//$this->db->query("DELETE FROM oc_servicearea WHERE `store_id` = '" . (int)$storeid . "'");
 		foreach($data['servicearea'] as $servicevalue)  
  	 	{
			
			   $query = $this->db->query("SELECT `store_id`, `pincode_no`, `delivery_charges`, `tax_class_id` FROM `oc_servicearea` WHERE `pincode_no`='" . $this->db->escape($servicevalue['pincode_no']). "' AND `store_id` = '" . (int)$storeid  . "'");
			  $filter_data = $query->rows;
			print_r(count ($filter_data));
			if(count ($filter_data) > 0){
			
			}else{

				$this->db->query("INSERT INTO " . DB_PREFIX . "servicearea SET   `store_id` = '" . (int)$storeid . "'  ,   
			`pincode_no` = '" . $this->db->escape($servicevalue['pincode_no']). "', 
			`delivery_charges`= '" . $this->db->escape($servicevalue['delivery_charges']) . "',
			`tax_class_id`= '" . $this->db->escape($servicevalue['tax_class_id']) . "' ");

			}
			// die();
			                
        }       
 	}
 	 return $storeid ;
 }

  public function editPin($storeid, $data) {
	foreach($data['servicearea'] as $servicevalue) 
	{
		$this->db->query("UPDATE " . DB_PREFIX . "servicearea SET  `store_id` = '". (int)$storeid . "'  , 
   	   `pincode_no` = '" . $this->db->escape($servicevalue['pincode_no']) . "',  
       `delivery_charges` = '" . $this->db->escape($servicevalue['delivery_charges']) . "' `tax_class_id`= '" . $this->db->escape($servicevalue['tax_class_id']) . "' ,servicearea WHERE `store_id` = '" . (int)$storeid . "'");
		
	   $this->cache->delete('servicearea');
	}
  }

	 public function deletePin($storeid) {
	$this->db->query("DELETE FROM " . DB_PREFIX . "servicearea WHERE `store_id` = '" . (int)$storeid . "'");
	
	$this->cache->delete('servicearea');		
	 }
 
	public function getPin($storeid) {
		$filter_data = $this->cache->get('servicearea');

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "servicearea WHERE store_id = " . $storeid );
		
		$query = $this->db->query("	SELECT  t.title , ssub.store_id, ssub.pincode_no, ssub.delivery_charges, ssub.tax_class_id FROM oc_servicearea ssub inner join oc_tax_class t on t.tax_class_id = ssub.tax_class_id WHERE store_id =   $storeid ");
		$filter_data = $query->rows;
				
		
		
		return $filter_data;
	}

	public function getTotalPin() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "servicearea");
		
		return $query->row['total'];
	}
}



 
