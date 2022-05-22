<?php
class ModelSaleReturn extends Model {

	



	public function addReturn($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', return_status_id = '" . (int)$data['return_status_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW()");
	
		return $this->db->getLastId();
	}

	public function editReturn($return_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
	}

	public function deleteReturn($return_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "return` WHERE `return_id` = '" . (int)$return_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "return_history` WHERE `return_id` = '" . (int)$return_id . "'");
	}

	// public function getReturn($return_id) {
	// 	$query = $this->db->query("SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS return_status FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "'");

	// 	return $query->row;
	// }
//---------------------------------------------------------------- New Functions added -------------------------------------------------------
	


    public function getOpenReturns($order_id) {
		$query = $this->db->query("SELECT r.return_id, r.order_id, r.product_id, r.firstname, r.lastname, r.email, r.telephone, r.product, r.model, r.quantity, r.opened, (SELECT rr.name FROM " . DB_PREFIX . "return_reason rr WHERE rr.return_reason_id = r.return_reason_id AND rr.language_id = '" . (int)$this->config->get('config_language_id') . "') AS reason, (SELECT ra.name FROM " . DB_PREFIX . "return_action ra WHERE ra.return_action_id = r.return_action_id AND ra.language_id = '" . (int)$this->config->get('config_language_id') . "') AS action, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, r.comment, r.request_type, r.date_ordered, r.date_added, r.date_modified FROM `" . DB_PREFIX . "return` r WHERE r.order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOpenCancel($order_id) {
		$query = $this->db->query("SELECT r.cancel_id, r.order_id, r.product_id, r.firstname, r.lastname, r.email, r.telephone, r.product, r.model, r.quantity, (SELECT rr.name FROM " . DB_PREFIX . "return_reason rr WHERE rr.return_reason_id = r.cancel_reason_id AND rr.language_id = '" . (int)$this->config->get('config_language_id') . "') AS reason, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.cancel_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, r.request_type, r.date_ordered, r.date_added, r.date_modified FROM `" . DB_PREFIX . "cancel` r WHERE r.order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOpenItemNotReceived($order_id) {
		$query = $this->db->query("SELECT r.item_not_received_id, r.order_id, r.product_id, r.firstname, r.lastname, r.email, r.telephone, r.product, r.model, r.quantity, r.item_not_received_reason AS reason, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.item_not_received_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, r.request_type, r.date_ordered, r.date_added, r.date_modified FROM `" . DB_PREFIX . "item_not_received` r WHERE r.order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getPaymentIdByOderId($order_id) {
		$query = $this->db->query("SELECT payment_id  FROM `" . DB_PREFIX . "order_history` WHERE order_status_id = 2 and order_id = '" . $order_id . "'");
		return $query->row['payment_id'];
	}
	public function getgrandTotalByOderId($order_id) {
		$query = $this->db->query("SELECT total  FROM `" . DB_PREFIX . "order` WHERE order_id = '" . $order_id . "'");
		return $query->row;
	}

	public function getReturns($data = array()) {


	

		$sql = "SELECT *, CONCAT(r.firstname, ' ', r.lastname) AS customer, (SELECT o.store_id FROM ". DB_PREFIX ."order o WHERE o.order_id = r.order_id  ) AS store_id,  (SELECT od.payment_code FROM ". DB_PREFIX ."order od WHERE od.order_id = r.order_id ) AS payment_mode ,(SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS return_status FROM `" . DB_PREFIX . "return` r";

		$implode = array();

		// if (!empty($data['filter_return_id'])) {
		// 	$implode[] = "r.return_id = '" . (int)$data['filter_return_id'] . "'";
		// }

		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}

		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}

		// if (!empty($data['filter_return_status_id'])) {
		// 	$implode[] = "r.return_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		// }

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'r.order_id',
			'customer',
			'r.product',
			'r.model',
			'status',
			'r.date_added',
			'r.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.return_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
    


	public function getItemNotReceived($data = array()) {
		$sql = "SELECT *, CONCAT(r.firstname, ' ', r.lastname) AS customer, r.item_not_received_status_id AS return_status_id , (SELECT o.store_id FROM ". DB_PREFIX ."order o WHERE o.order_id = r.order_id ) AS store_id,  (SELECT od.payment_code FROM ". DB_PREFIX ."order od WHERE od.order_id = r.order_id ) AS payment_mode ,(SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.item_not_received_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS return_status FROM `" . DB_PREFIX . "item_not_received` r";

		$implode = array();

		// if (!empty($data['filter_return_id'])) {
		// 	$implode[] = "r.return_id = '" . (int)$data['filter_return_id'] . "'";
		// }

		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}

		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}

		// if (!empty($data['filter_return_status_id'])) {
		// 	$implode[] = "r.item_not_received_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		// }

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'r.order_id',
			'customer',
			'r.product',
			'r.model',
			'status',
			'r.date_added',
			'r.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.item_not_received_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}


	public function getCancel($data = array()) {
		$sql = "SELECT *, CONCAT(r.firstname, ' ', r.lastname) AS customer, r.cancel_status_id AS return_status_id , (SELECT o.store_id FROM ". DB_PREFIX ."order o WHERE o.order_id = r.order_id ) AS store_id,  (SELECT od.payment_code FROM ". DB_PREFIX ."order od WHERE od.order_id = r.order_id ) AS payment_mode ,(SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.cancel_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS return_status FROM `" . DB_PREFIX . "cancel` r";

		$implode = array();

		// if (!empty($data['filter_return_id'])) {
		// 	$implode[] = "r.return_id = '" . (int)$data['filter_return_id'] . "'";
		// }

		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}

		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}

		// if (!empty($data['filter_return_status_id'])) {
		// 	$implode[] = "r.cancel_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		// }

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'r.order_id',
			'customer',
			'r.product',
			'r.model',
			'status',
			'r.date_added',
			'r.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.cancel_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function addReturnProduct($order_id, $product_id,$return_status_id) {
		
		$this->db->query("UPDATE `" . DB_PREFIX . "return` SET `return_status_id` = '" . (int)$return_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET `product_status_id` = '" . (int)$return_status_id . "'  WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "'");
	
	}

	
	public function addCancelProduct($order_id, $product_id,$cancel_status_id) {
		
		$this->db->query("UPDATE `" . DB_PREFIX . "cancel` SET `cancel_status_id` = '" . (int)$cancel_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET `product_status_id` = '" . (int)$cancel_status_id . "'  WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "'");
	
	}

	public function addItemNotReceivedProduct($order_id, $product_id, $item_not_received_status_id) {
		
		$this->db->query("UPDATE `" . DB_PREFIX . "item_not_received` SET `item_not_received_status_id` = '" . (int)$item_not_received_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET `product_status_id` = '" . (int)$item_not_received_status_id . "'  WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "'");
	
	}


	public function getTotalReturnRefundAmount($order_id){

		$sql = "SELECT r.request_type, (SELECT op.total FROM " . DB_PREFIX . "order_product op WHERE op.order_id = r.order_id AND op.product_id =  r.product_id ) AS total, (SELECT op.tax FROM " . DB_PREFIX . "order_product op WHERE op.order_id = r.order_id AND op.product_id =  r.product_id ) AS tax  FROM `" . DB_PREFIX . "return` r WHERE r.order_id = '" . (int)$order_id . "' AND r.request_type = 'Refund ' ";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalCancelRefundAmount($order_id){

		$sql = "SELECT r.request_type, (SELECT op.total FROM " . DB_PREFIX . "order_product op WHERE op.order_id = r.order_id AND op.product_id =  r.product_id ) AS total, (SELECT op.tax FROM " . DB_PREFIX . "order_product op WHERE op.order_id = r.order_id AND op.product_id =  r.product_id ) AS tax  FROM `" . DB_PREFIX . "cancel` r WHERE r.order_id = '" . (int)$order_id . "' AND r.request_type = 'Cancel ' ";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalItemNotReceivedRefundAmount($order_id){

		$sql = "SELECT r.request_type, (SELECT op.total FROM " . DB_PREFIX . "order_product op WHERE op.order_id = r.order_id AND op.product_id =  r.product_id ) AS total, (SELECT op.tax FROM " . DB_PREFIX . "order_product op WHERE op.order_id = r.order_id AND op.product_id =  r.product_id ) AS tax  FROM `" . DB_PREFIX . "item_not_received` r WHERE r.order_id = '" . (int)$order_id . "' AND r.request_type = 'Refund ' ";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getPaymentId($order_id){

		$sql = "SELECT r.request_type, (SELECT op.total FROM " . DB_PREFIX . "order_product op WHERE op.order_id = r.order_id AND op.product_id =  r.product_id ) AS total, (SELECT op.tax FROM " . DB_PREFIX . "order_product op WHERE op.order_id = r.order_id AND op.product_id =  r.product_id ) AS tax  FROM `" . DB_PREFIX . "cancel` r WHERE r.order_id = '" . (int)$order_id . "' AND r.request_type = 'Cancel ' ";
		$query = $this->db->query($sql);
		return $query->rows; 
	}

     
	public function setRefundSuccess($order_id){

		$sql = "SELECT r.product_id  FROM `" . DB_PREFIX . "return` r WHERE r.order_id = '" . (int)$order_id . "' AND r.request_type = 'Refund' ";
		$query = $this->db->query($sql);

       foreach($query->rows as $row){
        $return_status_id = 1;
		$this->db->query("UPDATE `" . DB_PREFIX . "return` SET `return_status_id` = '" . (int)$return_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$row['product_id'] . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET `product_status_id` = '" . (int)$return_status_id . "' WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$row['product_id'] . "'");
	}



	$sql = "SELECT r.product_id  FROM `" . DB_PREFIX . "cancel` r WHERE r.order_id = '" . (int)$order_id . "' AND r.request_type = 'Cancel' ";
		$query = $this->db->query($sql);

       foreach($query->rows as $row){
        $cancel_status_id = 1;
		$this->db->query("UPDATE `" . DB_PREFIX . "cancel` SET `cancel_status_id` = '" . (int)$cancel_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$row['product_id'] . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET `product_status_id` = '" . (int)$cancel_status_id . "' WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$row['product_id'] . "'");
	}


	}


 //------------------------------------------------------------------END-------------------------------------------------------------


	public function getTotalReturns($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return`r";

		$implode = array();

		if (!empty($data['filter_return_id'])) {
			$implode[] = "r.return_id = '" . (int)$data['filter_return_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . $this->db->escape($data['filter_order_id']) . "'";
		}

		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}

		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}

		if (!empty($data['filter_return_status_id'])) {
			$implode[] = "r.return_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalReturnsByReturnStatusId($return_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_status_id = '" . (int)$return_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalReturnsByReturnReasonId($return_reason_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_reason_id = '" . (int)$return_reason_id . "'");

		return $query->row['total'];
	}

	public function getTotalReturnsByReturnActionId($return_action_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_action_id = '" . (int)$return_action_id . "'");

		return $query->row['total'];
	}
	
	public function addReturnHistory($return_id, $return_status_id, $comment, $notify) {
		$this->db->query("UPDATE `" . DB_PREFIX . "return` SET `return_status_id` = '" . (int)$return_status_id . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "return_history` SET `return_id` = '" . (int)$return_id . "', return_status_id = '" . (int)$return_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape(strip_tags($comment)) . "', date_added = NOW()");
	}

	public function getReturnHistories($return_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify FROM " . DB_PREFIX . "return_history rh LEFT JOIN " . DB_PREFIX . "return_status rs ON rh.return_status_id = rs.return_status_id WHERE rh.return_id = '" . (int)$return_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY rh.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReturnHistories($return_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_history WHERE return_id = '" . (int)$return_id . "'");

		return $query->row['total'];
	}

	public function getTotalReturnHistoriesByReturnStatusId($return_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_history WHERE return_status_id = '" . (int)$return_status_id . "'");

		return $query->row['total'];
	}
}