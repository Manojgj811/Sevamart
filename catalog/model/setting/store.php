<?php
class ModelSettingStore extends Model {
	public function getStores() 
	{
		$store_data = $this->cache->get('store');
         if (!$store_data) 
		{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");

		// $query = $this->db->query("	SELECT s.store_id ,s.name, s.url ,sett.value 
		// from oc_store s 
        // inner join oc_setting sett 
		// ON s.store_id = sett.store_id 
        // inner join oc_servicearea sev 
		// ON s.store_id = sev.store_id 
		// WHERE  sett.key ='config_logo' ");

		$store_data = $query->rows;  
       $this->cache->set('store', $store_data);
         }
	return $store_data;

      }
	

		public function getStoresname($storenamesearch) {
	
			$query = $this->db->query("	SELECT s.store_id ,s.name, s.url ,sett.value 
			from oc_store s left join oc_setting sett 
			ON s.store_id = sett.store_id 
			WHERE  sett.key ='config_logo' AND s.name LIKE  '". trim($storenamesearch)."%'");

		$store_data = $query->rows;  
		return $store_data;
		}	

}