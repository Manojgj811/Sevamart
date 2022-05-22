<?php
class ModelStoreContractStoreContract extends Model 
{
 public function addStoreSubscription($data)
  {
	    //if Not EXISTS $this->db->query ("SELECT * FROM `" . DB_PREFIX . "store_subscription` WHERE store_id = '" . (int)$data['store_id'] . "'").
//$this->db->query("INSERT INTO " . DB_PREFIX . "store_subscription SET store_id = '" . (int)$data['store_id'] . "'");

   $this->db->query("INSERT INTO " . DB_PREFIX . "store_contract SET store_id = '" . (int)$data['store_id'] . "', commission_percentage_card = '" . $this->db->escape($data['commission_percentage_card']) . "', commission_percentage_cod = '" . $this->db->escape($data['commission_percentage_cod']) . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "'");
	$store_subscription_id = $this->db->getLastId();
        
 }

  public function editStoreSubscription($store_id,$data) 
  {
    $this->db->query("UPDATE " . DB_PREFIX . "store_contract SET store_id = '" . (int)$data['store_id'] . "',  commission_percentage_card = '" . $this->db->escape($data['commission_percentage_card']) . "', commission_percentage_cod = '" . $this->db->escape($data['commission_percentage_cod']) . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "' WHERE store_id = '" . (int)$store_id . "'");
  }

   public function deleteStoreSubscription($store_id) 
   {
    
    $this->db->query("DELETE FROM " . DB_PREFIX . "store_contract  WHERE store_id = '" . (int)$store_id . "'");
      
  }
 ////////////// required from line no 27 
  public function getStoreSubscription($store_id) 
  {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store_contract` WHERE store_id = '" . (int)$store_id . "'");

    return $query->row;
  }

  public function getStoresSubscriptions() 
     {
    $query=$this->db->query("SELECT s.name as Storename,t.title as Gsttitle, s.store_id, t.tax_class_id, ssub.store_contract_id,
      ssub.commission_percentage_card, ssub.commission_percentage_cod, ssub.tax_class_id FROM oc_store_contract ssub inner join oc_store s on s.store_id=ssub.store_id inner join oc_tax_class t on t.tax_class_id = ssub.tax_class_id");
       return $query->rows;
      }

  public function getStoresSubscription2($store_id) 
  {
    $query=$this->db->query("SELECT s.name as Storename,t.title as Gsttitle, s.store_id, t.tax_class_id, ssub.store_contract_id,
      ssub.commission_percentage_card, ssub.commission_percentage_cod, ssub.tax_class_id FROM oc_store_contract ssub inner join oc_store s on s.store_id=ssub.store_id 
      inner join oc_tax_class t on t.tax_class_id = ssub.tax_class_id   where ssub.store_id= '" . (int)$store_id . "' " );

    return $query->rows;
    }


}





 
















