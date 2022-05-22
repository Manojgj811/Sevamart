<?php
class ModelAccountItemNotReceived extends Model {
  
	public function updateProductStatus($data){
        
		$product_status_id = 4;
		$this->db->query("UPDATE " . DB_PREFIX . "order_product SET product_status_id = '" . (int)$product_status_id. "' WHERE order_id = '" . (int)$data['order_id'] . "' AND product_id = '" . (int)$data['product_id'] . "' ");

	}
	
	public function getRequestedDate($order_id, $product_id){
        
		
		$query = $this->db->query("SELECT date_added , request_type FROM `" . DB_PREFIX . "item_not_received` WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "' ");

		return $query->row;
	}


	public function addItemNotReceived($data) {

		$config_item_not_received_status_id = 4;
		$this->db->query("INSERT INTO `" . DB_PREFIX . "item_not_received` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$this->customer->getId() . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', item_not_received_status_id = '" . (int)$config_item_not_received_status_id . "',request_type = '" . $this->db->escape($data['request_type']) . "' , date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW()");

		return $this->db->getLastId();
	}
	
	

	public function getReturn($return_id) {
		$query = $this->db->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, r.email, r.telephone, r.product, r.model, r.quantity, r.opened, (SELECT rr.name FROM " . DB_PREFIX . "return_reason rr WHERE rr.return_reason_id = r.return_reason_id AND rr.language_id = '" . (int)$this->config->get('config_language_id') . "') AS reason, (SELECT ra.name FROM " . DB_PREFIX . "return_action ra WHERE ra.return_action_id = r.return_action_id AND ra.language_id = '" . (int)$this->config->get('config_language_id') . "') AS action, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, r.comment, r.date_ordered, r.date_added, r.date_modified FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "' AND r.customer_id = '" . $this->customer->getId() . "'");

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