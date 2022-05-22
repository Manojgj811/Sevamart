<?php
class ModelSalePosreturn extends Model {

    public function getOrders( $customer_id) {
        $start = 0;
        $limit = 800;
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname,o.store_name,o.store_id, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$customer_id . "' AND o.order_status_id > '0' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

    public function getOrderProducts($order_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		$query = $this->db->query("SELECT *,(SELECT ordertax.delivery_charge FROM oc_order_tax ordertax WHERE ordertax.order_id = orderproduct.order_id) AS deliverycharge 
		FROM oc_order_product orderproduct WHERE orderproduct.order_id='" . (int)$order_id . "' ");
	
		return $query->rows;
	}


    public function getProductStatus($order_id, $product_id){
        
		
		$query = $this->db->query("SELECT product_status_id FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "' ");

		return $query->row['product_status_id'];
	}
	
	
	public function getRequestedDate($order_id, $product_id){
        
		
		$query = $this->db->query("SELECT date_added , request_type FROM `" . DB_PREFIX . "return` WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "' ");

		return $query->row;
	}


    public function getProductImage($product_id) {
		//$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		$query = $this->db->query("SELECT  image, (SELECT p.name FROM ". DB_PREFIX ."product_description p WHERE p.product_id = '" . (int)$product_id . "' ) AS name FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$product_id . "'");
		
		if ($query->num_rows) {
			return array(
				
				'image'            => $query->row['image'],
				'name'             => $query->row['name']
				
			);
		} else {
			return false;
		}
	}


}