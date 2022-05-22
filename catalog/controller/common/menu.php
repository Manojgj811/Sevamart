<?php
class ControllerCommonMenu extends Controller {
	public function index() {
		$this->load->language('common/menu');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		//$categories = $this->model_catalog_category->getCategories(0);
		$categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {
                // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {

                    // Level 3
                    $grandchildren_data = array();

                    $grandchildren = $this->model_catalog_category->getCategories($child['category_id']);



                    foreach ($grandchildren as $grandchild) {
                    //level 4
                        $grandgrandchildren_data = array();

                        $grandgrandchildren= $this->model_catalog_category->getCategories($grandchild['category_id']);
    

                        foreach ($grandgrandchildren as $grandgrandchild) {

                      //level5

                      $grandgrandchildren_data1 = array();

                      $grandgrandchildren1= $this->model_catalog_category->getCategories($grandgrandchild['category_id']);
  
                      foreach ($grandgrandchildren1 as $grandgrandchild1) {

                        $grandgrandchild_filter_data1 = array(
                            'filter_category_id'  => $grandgrandchild1['category_id'],
                            'filter_sub_category' => true
                        );

                        $grandgrandchildren_data1[] = array(
                            'name'  => $grandgrandchild1['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($grandgrandchild_filter_data1) . ')' : ''),
                            'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_'. $child['category_id'] .'_' . $grandchild['category_id'] .'_' . $grandgrandchild['category_id'].'_'. $grandgrandchild1['category_id'])
                           
                        );

                    }

                            $grandgrandchild_filter_data = array(
                                'filter_category_id'  => $grandgrandchild['category_id'],
                                'filter_sub_category' => true
                            );
    
                            $grandgrandchildren_data[] = array(
                                'name'  => $grandgrandchild['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($grandgrandchild_filter_data) . ')' : ''),
                                'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] .'_' . $grandchild['category_id'] .'_' . $grandgrandchild['category_id']),
                                'children' =>$grandgrandchildren_data1,
                            );

                        }



                        $grandchild_filter_data = array(
                            'filter_category_id'  => $grandchild['category_id'],
                            'filter_sub_category' => true
                        );

                        $grandchildren_data[] = array(
                            'name'  => $grandchild['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($grandchild_filter_data) . ')' : ''),
                            'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] .'_' . $grandchild['category_id']),
                            'children' =>$grandgrandchildren_data,
                        );
                    }


                    $filter_data = array(
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    $children_data[] = array(
                        'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
                        'children' => $grandchildren_data,
                    );
                }

                // Level 1-category
                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'children' => $children_data,
                    'column'   => $category['column'] ? $category['column'] : 1,
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
            }
        }

        return $this->load->view('common/menu', $data);
    }
}