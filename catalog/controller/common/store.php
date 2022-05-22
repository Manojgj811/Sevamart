

<?php
class ControllerCommonStore extends Controller 
{
	//home page for individual store
	public function index() 
	{

		
		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		
		$this->load->model('tool/image');

		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

       if (isset($this->request->get['route'])) 
		{
		$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		//var_dump($this->config->get('config_url'));

		
		$data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);
		//var_dump( $categories,"<br>","<br>","<br>");

		  foreach ($categories as $category) {
			// echo $category['top'];
			// echo "<br>";
            if ($category['top']) {
                 // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);
			//var_dump(  $children,"<br>","<br>","<br>");

			
          foreach ($children as $child) {

					$filter_data = array(
					'filter_category_id'  => $child['category_id'],
					'filter_sub_category' => true
				);

				// var_dump($filter_data);
				// 	echo "<br>";

				// Level 1
				$data['categories'][] = array(
					'name'     => $child['name'],
					'id'     => $child['category_id']
					
				);

				
					$data['products'] = array();

					
		
					$data['products'] = $this->model_catalog_product->getProducts($filter_data);
				
				//  var_dump($data['products'],"<br>","<br>","<br>");
				//  echo"<br>";
				//  echo"<br>";
				//  echo"<br>";

				// die();

          	foreach ($data['products'] as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}


					$url='';			
				
			       $data['m'][] = array(
					'category_id'  => $child['category_id'],
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					 'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'quantity'  => $result['quantity'],
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product',  'product_id=' . $result['product_id'] . $url)
				 
				);

				
			}

	
				
			// var_dump($data['m']['category_id'],"<br>","<br>","<br>");
			//die();
		}
			
    }
 }	
//  $aRanks['b'] = array();
// 				foreach($data['m'] as $aEntry) {
// 					$aRanks['b'][$aEntry['thumb']][] = $aEntry;

// 				}
				
// 				var_dump($aRanks['b'],"<br>","<br>","<br>");


//  echo"<br>"; echo"<br>";	
// var_dump($child['category_id'],"<br>","<br>","<br>");


			// echo"<br>";

$cartProducts2 = $this->cart->getProducts();

$data['myVariable']=array();

foreach($cartProducts2 as $cart)
{ 

	$data['myVariable'][]=array($cart['product_id']);

		$data['openids'][]=array(
		'product_id'=> $cart['product_id'],
		'quantity'=> $cart['quantity'],
		'cart_id'=> $cart['cart_id']
		);

$data['test'][]=[$cart['product_id']];




$array = array( $cart['product_id'] );

$allKeys = array_values($array);

			  
$data['key'][]=$allKeys[0];

// echo  $Keys[0];

//var_dump($data['openids']);


//   echo  $allKeys[0].',';

}


       	$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		/// newly created
		
		$data['wishlist1'] = $this->url->link('account/wishlist1', '', true);
		
		//newly added header1 and search 1 for pincode search purpose
	    $data['search'] =$this->load->controller('common/search');
	
       $this->response->setOutput($this->load->view('common/home', $data));

	}
}
