<?php
class ModelStorecontractStorecontract extends Model {

public function addStorecontract($data)
{
  $query = $this->db->query("SELECT `store_contract_id`, `store_id`, `commission_percentage_card`, `commission_percentage_cod`, `tax_class_id`, `GSTIN`, `razorpay_key_id`, `razorpay_key_secret` FROM `oc_store_contract` WHERE `store_id`= '" . (int)$data['store_id'] . "' ");
  $filter_data = $query->rows;
    print_r(count ($filter_data));
      if(count ($filter_data) > 0){

    }else{
$this->db->query("INSERT INTO " . DB_PREFIX . "store_contract SET store_id = '" . (int)$data['store_id'] . "', commission_percentage_card = '" . $this->db->escape($data['commission_percentage_card']) . "', 
commission_percentage_cod = '" . $this->db->escape($data['commission_percentage_cod']) . "',
 tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', GSTIN = '" . $this->db->escape($data['GSTIN']) . "', razorpay_key_id = '" . $this->db->escape($data['payment_razorpay_key_id']) . "', razorpay_key_secret = '" . $this->db->escape($data['payment_razorpay_key_secret']) . "'");

  $store_contract_id = $this->db->getLastId();
      
  }
}

    public function editStorecontract($store_id,$data) 
    {
        // $this->db->query("UPDATE " . DB_PREFIX . "store_contract SET store_id = '" . (int)$data['store_id'] . "', 
        // commission_percentage_card = '" . $this->db->escape($data['commission_percentage_card']) . "',   account_id = '" . $this->db->escape($data['account_id']) . "',
        //   commission_percentage_cod = '" . $this->db->escape($data['commission_percentage_cod']) . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', GSTIN = '" . $this->db->escape($data['GSTIN']) . "' WHERE store_id = '" . (int)$store_id . "'");
        
        $this->db->query("UPDATE " . DB_PREFIX . "store_contract SET 
        commission_percentage_card = '" . $this->db->escape($data['commission_percentage_card']) . "',   
          commission_percentage_cod = '" . $this->db->escape($data['commission_percentage_cod']) . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', GSTIN = '" . $this->db->escape($data['GSTIN']) . "' , razorpay_key_id = '" . $this->db->escape($data['payment_razorpay_key_id']) . "', razorpay_key_secret = '" . $this->db->escape($data['payment_razorpay_key_secret']) . "' WHERE store_id = '" . (int)$store_id . "'");
        
     }

public function deleteStorecontract($store_contract_id) {

  $this->db->query("DELETE FROM oc_store_contract WHERE store_contract_id = '" . (int)$store_contract_id . "'");
    
  }

public function getStorecontract($store_id) {
$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store_contract` WHERE store_id = '" . (int)$store_id . "'");

return $query->row;

}


  public function getStorescontracts() {

$query=$this->db->query("SELECT s.name as Storename,  t.title as Gsttitle, s.store_id, t.tax_class_id, ssub.store_contract_id, ssub.commission_percentage_card, 
ssub.commission_percentage_cod, ssub.tax_class_id, ssub.GSTIN, ssub.razorpay_key_id, ssub.razorpay_key_secret FROM oc_store_contract ssub inner join oc_store s on s.store_id=ssub.store_id inner join oc_tax_class t on t.tax_class_id = ssub.tax_class_id");

  return $query->rows;
  }


  }






















