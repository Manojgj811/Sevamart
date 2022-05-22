<?php
class ControllerExtensionReportSaleTax extends Controller {
	public function index() {
		$this->load->language('extension/report/sale_tax');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('report_sale_tax', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/report/sale_tax', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/report/sale_tax', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true);

		if (isset($this->request->post['report_sale_tax_status'])) {
			$data['report_sale_tax_status'] = $this->request->post['report_sale_tax_status'];
		} else {
			$data['report_sale_tax_status'] = $this->config->get('report_sale_tax_status');
		}

		if (isset($this->request->post['report_sale_tax_sort_order'])) {
			$data['report_sale_tax_sort_order'] = $this->request->post['report_sale_tax_sort_order'];
		} else {
			$data['report_sale_tax_sort_order'] = $this->config->get('report_sale_tax_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/report/sale_tax_form', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/report/sale_tax')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function report() {
		$this->load->language('extension/report/sale_tax');
		
		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		if (isset($this->request->get['filter_group'])) {
			$filter_group = $this->request->get['filter_group'];
		} else {
			$filter_group = 'week';
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = 0;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$this->load->model('extension/report/sale');

		$data['orders'] = array();

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_group'           => $filter_group,
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_extension_report_sale->getTotalTaxes($filter_data);

        $data['orders'] = array();

      $this->load->model('user/user');
				
		$user_info = $this->model_user_user->getUser($this->user->getId());
////changes done on 16-04-2021  for 
			

				if ($user_info) 
					{
						$usercount=0;

						$data['user_group'] = $user_info['user_group_id'];

					$this->load->model('setting/store');

						$this->load->model('setting/setting');

						$storeresults = $this->model_setting_store->getStores();

						if($data['user_group']==1)
						{
							foreach($storeresults as $result) 
							{
							$data['stores'][] = array(
							'name' => $result['name'],
							'storeid'=>$result['store_id']
								);
							}
				
			$results = $this->model_extension_report_sale->getTaxes($filter_data);
     
			//var_dump($results);
			//echo "<br>";
	          	if(count($results)>0)
					{
						$rate_sum = 0;
						$sgst=0;
						$totalamountgrandtotal=0;
						$taxgrandtotal=0;

					foreach ($results as $result)
					 {
						$z=$result['tax'];
						$zz=$result['Secondtax'];
						//var_dump( $z);

				    	// $myArray = explode(',', $z);
						// $first=$myArray[0];
						// $rate_sum += $first;

						$rate_sum += $z;
						//@$a=$myArray[1]  echo "  the first index value is $a<br>";;
						// $myArray2 = explode(',', $zz);
						// @$second=$myArray2[1];

						 $sgst+=$zz;

						 $invoiceprefix=$result['invoice_prefix'].$result['invoice_no'];
						$totalamountgrandtotal+=$result['totalamount'];

						$taxgrandtotal+=$result['total'];

                     //     echo "  the zero  index value is $first<br>";
						//echo "  the first  index value is $second<br>";
              //  echo "<br>";
                     
						$usercount+=1;
						$data['orders'][] = array(
							'serialno' =>  $usercount,
							'date_start' => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
						//	'date_end'   => date($this->language->get('date_format_short'), strtotime($result['date_end'])),
							'orderid'   =>$result['order_id'],
							'invoice'    =>$invoiceprefix,

							'taxes'     =>$result['tax'],
							'taxesss'     =>$result['Secondtax'],

							'totalgstvalue'=>$rate_sum,

							//'gST'         =>$result['Cgsttotal'],
							// 'sGST'       =>$result['Sgsttotal'],
							'store'      =>$result['store_id'],
							//'title'      => $result['title'],
							//'orders'     => $result['orders'],
							'amount'    =>  $result['totalamount'],
							'total'      => $result['total']
										);
										
						}

            

						$data['cgst']=$rate_sum;
					//echo  $data['cgst'] ;
					$data['sgst']=	$sgst;
					//echo $data['sgst'];

                     $data['totalamountgrandtotal']=$totalamountgrandtotal;
					 $data['taxgrandtotal']=$taxgrandtotal;
		     	}

	        	}
			}
				
		/////for store managers
		if($user_info)
	      {
			$usercount=0;
			$orderamount='';
	     $data['storeid']= $user_info['store_id'];
		  
	      $data['user_group'] = $user_info['user_group_id'];

				  if($data['user_group']==11)
				  {
				$this->load->model('setting/store');
				  $storeresults2 = $this->model_setting_store->getStores();
				  
					foreach ($storeresults2 as $storeresult) 
					{
						$storeid=$storeresult['store_id'];
					
					if($data['storeid']==$storeid)
					{
						$results2=$this->model_extension_report_sale->getTaxesbystore($filter_data ,$storeid);
                  	}
					}

         

	         if(count($results2)>0)
			{
				$rate_sum = 0;
				$sgst=0;
				$totalamountgrandtotal=0;
				$taxgrandtotal=0;

			foreach ($results2 as $result)
			 {
				$usercount+=1;

				$z=$result['tax'];
				
				$zz=$result['Secondtax'];

   		$invoiceprefix=$result['invoice_prefix'].$result['invoice_no'];
			
					$rate_sum += $z;
				//@$a=$myArray[1]  echo "  the first index value is $a<br>";;
			
				$sgst+=$zz;
				
				$totalamountgrandtotal+=$result['totalamount'];

				$taxgrandtotal+=$result['total'];
             
						$data['orders'][]=array(
						'serialno' =>  $usercount,
					'date_start' => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
						'orderid'   =>$result['order_id'],
						'invoice'    =>$invoiceprefix,
						'STORE'       =>$result['store_id'],
					      'GST'       =>$result['tax'],
						  'SGST'        =>$result['Secondtax'],
						'amount'      =>$result['totalamount'],
					
						'total'      => $result['total']
									);
					}


             
						$data['cgst']=$rate_sum;
					//	echo  $data['cgst'] ;
						$data['sgst']=	$sgst;
					//	echo $data['sgst'];
	
						 $data['totalamountgrandtotal']=$totalamountgrandtotal;
						 $data['taxgrandtotal']=$taxgrandtotal;

				
				}
				}
			}
		

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['groups'] = array();

		$data['groups'][] = array(
			'text'  => $this->language->get('text_year'),
			'value' => 'year',
		);

		$data['groups'][] = array(
			'text'  => $this->language->get('text_month'),
			'value' => 'month',
		);

		$data['groups'][] = array(
			'text'  => $this->language->get('text_week'),
			'value' => 'week',
		);

		$data['groups'][] = array(
			'text'  => $this->language->get('text_day'),
			'value' => 'day',
		);

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_group'])) {
			$url .= '&filter_group=' . $this->request->get['filter_group'];
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=sale_tax' . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_group'] = $filter_group;
		$data['filter_order_status_id'] = $filter_order_status_id;

		return $this->load->view('extension/report/sale_tax_info', $data);
	}
}