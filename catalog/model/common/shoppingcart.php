<?php
class ModelCommonShoppingCart extends Model {
	
	public function getCustomercartstore() {
        $query = $this->db->query("SELECT s.name as Storename,s.url as Storeurl, ssub.customer_id, ssub.store_id FROM oc_cart ssub inner join oc_store s on s.store_id=ssub.store_id where ssub.customer_id = '" . (int)$this->customer->getId() . "'");
	//var_dump($query);
		return $query->rows;
	}

	
}
