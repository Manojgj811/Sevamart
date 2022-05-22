<?php
class ModelAccountWishlist1 extends Model {
	public function addWishlist($store_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_stores_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND store_id = '" . (int)$store_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_stores_wishlist SET customer_id = '" . (int)$this->customer->getId() . "', store_id = '" . (int)$store_id . "', date_added = NOW()");
	}

	public function deleteWishlist($store_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_stores_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND store_id = '" . (int)$store_id . "'");
	
	}


	public function getWishlist() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_stores_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");
//var_dump($query);
		return $query->rows;
	}

	public function getTotalWishlist() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_stores_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}
}
