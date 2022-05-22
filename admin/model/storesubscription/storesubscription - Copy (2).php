<?php
class ModelStoresubscriptionStoresubscription extends Model {

  public function addStoreSubscription($data)
  {
    
  $this->db->query("INSERT INTO " . DB_PREFIX . "store_subscription SET store_id = '" . (int)$data['store_id'] . "', commission_percentage_card = '" . $this->db->escape($data['commission_percentage_card']) . "', commission_percentage_cod = '" . $this->db->escape($data['commission_percentage_cod']) . "', GST_commission_percentage = '" . $this->db->escape($data['GST_commission_percentage']) . "'");

		$store_subscription_id = $this->db->getLastId();
        
 }

 public function editStoreSubscription($data) {
  $this->db->query("UPDATE " . DB_PREFIX . "store_subscription SET store_id = '" . (int)$data['store_id'] . "',  commission_percentage_card = '" . $this->db->escape($data['commission_percentage_card']) . "', commission_percentage_cod = '" . $this->db->escape($data['commission_percentage_cod']) . "', GST_commission_percentage = '" . $this->db->escape($data['GST_commission_percentage']) . "' WHERE store_subscription_id = '" . (int)$store_subscription_id . "'");
}

public function deleteStoreSubscription($data) {
  $this->db->query("DELETE FROM " . DB_PREFIX . "store_subscription WHERE store_id = '" . (int)$data['store_id'] . "'");
}

public function getStoreSubscription($data) {
  $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store_subscription` WHERE store_id = '" . (int)$data['store_id'] . "'");

  return $query->row;
}

public function getStoresSubscriptions($data = array()) {
		
	$store_subscription_info = $this->cache->get('store_subscription');

		if (!$store_subscription_info) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store_subscription ORDER BY store_id  ");

		
	$store_subscription_info = $query->rows;
	var_dump($query);

			$this->cache->set('store_subscription', $store_subscription_info);
		}

		return $store_subscription_info;
	}


	public function getTotalStoreSubscription() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "store_subscription`");

		return $query->row['total'];
	}


}

  





 


















