<?php
class ModelAccountTrackItems extends Model {
  
	public function getRequestedDate($order_id, $product_id){
        
		
		$query = $this->db->query("SELECT date_added , request_type FROM `" . DB_PREFIX . "return` WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "' ");

		return $query->row;
	}


	public function getReturn() {
		$query = $this->db->query("SELECT r.return_id, r.order_id, r.product_id,r.firstname, r.lastname, r.email, r.telephone, r.product, r.model, r.quantity, (SELECT o.store_id FROM ". DB_PREFIX ."order o WHERE o.order_id = r.order_id ) AS store_id, (SELECT sn.store_name FROM ". DB_PREFIX ."order sn WHERE sn.order_id = r.order_id ) AS store_name ,(SELECT rr.name FROM " . DB_PREFIX . "return_reason rr WHERE rr.return_reason_id = r.return_reason_id AND rr.language_id = '" . (int)$this->config->get('config_language_id') . "') AS reason, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, r.request_type ,r.date_ordered, r.date_added, r.date_modified FROM `" . DB_PREFIX . "return` r WHERE  r.customer_id = '" . $this->customer->getId() . "'");

		return $query->rows;
	}


	public function getCancel() {
		$query = $this->db->query("SELECT r.cancel_id, r.order_id, r.product_id, r.firstname, r.lastname, r.email, r.telephone, r.product, r.model, r.quantity,  (SELECT o.store_id FROM ". DB_PREFIX ."order o WHERE o.order_id = r.order_id ) AS store_id, (SELECT sn.store_name FROM ". DB_PREFIX ."order sn WHERE sn.order_id = r.order_id ) AS store_name, (SELECT rr.name FROM " . DB_PREFIX . "cancel_reason rr WHERE rr.cancel_reason_id = r.cancel_reason_id AND rr.language_id = '" . (int)$this->config->get('config_language_id') . "') AS reason, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.cancel_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, r.request_type ,r.date_ordered, r.date_added, r.date_modified FROM `" . DB_PREFIX . "cancel` r WHERE  r.customer_id = '" . $this->customer->getId() . "'");

		return $query->rows;
	}

	public function getItemNotReceived() {
		$query = $this->db->query("SELECT r.item_not_received_id, r.order_id, r.product_id, r.firstname, r.lastname, r.email, r.telephone, r.product, r.model, r.quantity,  (SELECT o.store_id FROM ". DB_PREFIX ."order o WHERE o.order_id = r.order_id ) AS store_id, (SELECT sn.store_name FROM ". DB_PREFIX ."order sn WHERE sn.order_id = r.order_id ) AS store_name ,(SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.item_not_received_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, r.request_type ,r.date_ordered, r.date_added, r.date_modified FROM `" . DB_PREFIX . "item_not_received` r WHERE  r.customer_id = '" . $this->customer->getId() . "'");

		return $query->rows;
	}

	public function getOrderProductPrice($order_id, $product_id) 
	{
		$query = $this->db->query("SELECT r.total, r.tax FROM `" . DB_PREFIX . "order_product` r WHERE   r.order_id = '" . $order_id . "' and r.product_id = '" . $product_id . "'");
		return $query->row;
	}

	public function getOpenReturns($order_id) {
		$query = $this->db->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, r.email, r.telephone, r.product, r.model, r.quantity, r.opened, (SELECT rr.name FROM " . DB_PREFIX . "return_reason rr WHERE rr.return_reason_id = r.return_reason_id AND rr.language_id = '" . (int)$this->config->get('config_language_id') . "') AS reason, (SELECT ra.name FROM " . DB_PREFIX . "return_action ra WHERE ra.return_action_id = r.return_action_id AND ra.language_id = '" . (int)$this->config->get('config_language_id') . "') AS action, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, r.comment, r.request_type, r.date_ordered, r.date_added, r.date_modified FROM `" . DB_PREFIX . "return` r WHERE r.order_id = '" . (int)$order_id . "' AND r.customer_id = '" . $this->customer->getId() . "'");
		return $query->rows;
	}

	public function getReturns($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, rs.name as status, r.date_added FROM `" . DB_PREFIX . "return` r LEFT JOIN " . DB_PREFIX . "return_status rs ON (r.return_status_id = rs.return_status_id) WHERE r.customer_id = '" . (int)$this->customer->getId() . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.return_id DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}


	public function getRequestedReturn($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, r.product, rs.name as status, r.date_added FROM `" . DB_PREFIX . "return` r LEFT JOIN " . DB_PREFIX . "return_status rs ON (r.return_status_id = rs.return_status_id) WHERE r.customer_id = '" . (int)$this->customer->getId() . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.return_id DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReturns() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return`WHERE customer_id = '" . $this->customer->getId() . "'");

		return $query->row['total'];
	}

	public function getReturnHistories($return_id) {
		$query = $this->db->query("SELECT rh.date_added, rs.name AS status, rh.comment FROM " . DB_PREFIX . "return_history rh LEFT JOIN " . DB_PREFIX . "return_status rs ON rh.return_status_id = rs.return_status_id WHERE rh.return_id = '" . (int)$return_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY rh.date_added ASC");

		return $query->rows;
	}
}
