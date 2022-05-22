<?php
class ControllerSalePosMenu extends Controller {
	public function index() {
		$this->load->language('common/menu');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('sale/poscategory');

		$this->load->model('user/user');
				
		$user_info = $this->model_user_user->getUser($this->user->getId());

		$storemanager_storeID=$user_info['store_id'];

		$data['categories'] = array();

		$categories = $this->model_sale_poscategory->getCategories(0 ,$storemanager_storeID);

	
        foreach ($categories as $category) {
            if ($category['top']) {
                // Level 2
                $children_data = array();

                $children = $this->model_sale_poscategory->getCategories($category['category_id'],$storemanager_storeID);

                foreach ($children as $child) {

                    // Level 3
                    $grandchildren_data = array();

                    $grandchildren = $this->model_sale_poscategory->getCategories($child['category_id'],$storemanager_storeID);



                    foreach ($grandchildren as $grandchild) {
                    //level 4
                        $grandgrandchildren_data = array();

                        $grandgrandchildren= $this->model_sale_poscategory->getCategories($grandchild['category_id'],$storemanager_storeID);
    

                        foreach ($grandgrandchildren as $grandgrandchild) {

                      //level5

                      $grandgrandchildren_data1 = array();

                      $grandgrandchildren1= $this->model_sale_poscategory->getCategories($grandgrandchild['category_id'],$storemanager_storeID);
  
                      foreach ($grandgrandchildren1 as $grandgrandchild1) {

                        $grandgrandchild_filter_data1 = array(
                            'filter_category_id'  => $grandgrandchild1['category_id'],
                            'filter_sub_category' => true
                        );

                        $grandgrandchildren_data1[] = array(
                            'name'  => $grandgrandchild1['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($grandgrandchild_filter_data1) . ')' : ''),
                            'href'  => $this->url->link('product/category', 'user_token=' .$this->session->data['user_token'] .'&path=' . $category['category_id'] . '_'. $child['category_id'] .'_' . $grandchild['category_id'] .'_' . $grandgrandchild['category_id'].'_'. $grandgrandchild1['category_id'])
                           
                        );

                    }

                            $grandgrandchild_filter_data = array(
                                'filter_category_id'  => $grandgrandchild['category_id'],
                                'filter_sub_category' => true
                            );
    
                            $grandgrandchildren_data[] = array(
                                'name'  => $grandgrandchild['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($grandgrandchild_filter_data) . ')' : ''),
                                'href'  => $this->url->link('product/category', 'user_token=' .$this->session->data['user_token'] .'&path=' . $category['category_id'] . '_' . $child['category_id'] .'_' . $grandchild['category_id'] .'_' . $grandgrandchild['category_id']),
                                'children' =>$grandgrandchildren_data1,
                            );

                        }



                        $grandchild_filter_data = array(
                            'filter_category_id'  => $grandchild['category_id'],
                            'filter_sub_category' => true
                        );

                        $grandchildren_data[] = array(
                            'name'  => $grandchild['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($grandchild_filter_data) . ')' : ''),
                            'href'  => $this->url->link('product/category', 'user_token=' .$this->session->data['user_token'] .'&path=' . $category['category_id'] . '_' . $child['category_id'] .'_' . $grandchild['category_id']),
                            'children' =>$grandgrandchildren_data,
                        );
                    }


                    $filter_data = array(
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    $children_data[] = array(
                        'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_sale_poscategory->getTotalProducts($filter_data,$storemanager_storeID ) . ')' : ''),
                        'href'  => $this->url->link('product/category', 'user_token=' .$this->session->data['user_token'] .'&path=' . $category['category_id'] . '_' . $child['category_id']),
                        'children' => $grandchildren_data,
                    );
                }

                // Level 1
                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'children' => $children_data,
                    'column'   => $category['column'] ? $category['column'] : 1,
                    'href'     => $this->url->link('product/category', 'user_token=' .$this->session->data['user_token'] .'&path=' . $category['category_id'])
                );
            }
        }


			return $this->load->view('sale/posmenu', $data);

		}
	}
