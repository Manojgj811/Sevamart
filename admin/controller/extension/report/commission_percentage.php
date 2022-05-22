<?php
class ControllerExtensionReportCommissionPercentage extends Controller {
	public function index() {
		$this->load->language('extension/report/commission_percentage');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('report_commission_percentage', $this->request->post);

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
			'href' => $this->url->link('extension/report/commission_percentage', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/report/commission_percentage', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true);

		if (isset($this->request->post['report_commission_percentage_status'])) {
			$data['report_commission_percentage_status'] = $this->request->post['report_commission_percentage_status'];
		} else {
			$data['report_commission_percentage_status'] = $this->config->get('report_commission_percentage_status');
		}

		if (isset($this->request->post['report_commission_percentage_sort_order'])) {
			$data['report_commission_percentage_sort_order'] = $this->request->post['report_commission_percentage_sort_order'];
		} else {
			$data['report_commission_percentage_sort_order'] = $this->config->get('report_commission_percentage_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/report/commission_percentage_form', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/report/commission_percentage')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function report() {
		$this->load->language('extension/report/commission_percentage');
		
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

		$this->load->model('extension/report/commission_percentage');

		$data['orders'] = array();

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_group'           => $filter_group,
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_extension_report_commission_percentage->getTotalCommission($filter_data);

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
				
			$results = $this->model_extension_report_commission_percentage->getCommission($filter_data);
     
	          	if(count($results)>0)
					{
						$sub_total               =0;
						$cgst_order_amount       =0;
						$sgst_order_amount       =0;
						$cgst_delivery_amount    =0;
						$sgst_delivery_amount    =0;
						$commission_amount       =0;
						$cgst_commission_amount  =0;
						$sgst_commission_amount  =0;
						$store_id                =0;
						$total_commission_amount =0;
						
					foreach ($results as $result)
					 {
						$invoiceprefix=$result['invoice_prefix'].$result['invoice_no'];
						$sub_total               += $result['sub_total'];
						$cgst_order_amount       += $result['cgst_order_amount'];
						$sgst_order_amount       += $result['sgst_order_amount'];
						$cgst_delivery_amount    += $result['cgst_delivery_amount'];
						$sgst_delivery_amount    += $result['sgst_delivery_amount'];
						$commission_amount       += $result['commission_amount'];
						$cgst_commission_amount  += $result['cgst_commission_amount'];
						$sgst_commission_amount  += $result['sgst_commission_amount'];
						$total_commission_amount += $result['total_commission_amount'];
						
			
						$usercount+=1;
						$data['orders'][] = array(
							'serialno'                   =>$usercount,
							'date_start'                 =>date($this->language->get('date_format_short'), strtotime($result['date_start'])),
							'orderid'                    =>$result['order_id'],
							'invoice'                    =>$invoiceprefix,
							'sub_total'                  =>$result['sub_total'],
							'payment_method'             =>$result['Mode_of_payment'],	
							'cgst_order_amount'          =>$result['cgst_order_amount'],
							'sgst_order_amount'          =>$result['sgst_order_amount'],
							'delivery_charge'            =>$result['delivery_charge'],
							'cgst_delivery_percentage'   =>$result['cgst_delivery_percentage'],
							'cgst_delivery_amount'       =>$result['cgst_delivery_amount'],
							'sgst_delivery_percentage'   =>$result['sgst_delivery_percentage'],
							'sgst_delivery_amount'       =>$result['sgst_delivery_amount'],
							'commission_percentage'      =>$result['commission_percentage'],
							'commission_amount'          =>$result['commission_amount'],
							'cgst_commission_percentage' =>$result['cgst_commission_percentage'],
							'cgst_commission_amount'     =>$result['cgst_commission_amount'],
							'sgst_commission_percentage' =>$result['sgst_commission_percentage'],
							'sgst_commission_amount'     =>$result['sgst_commission_amount'],
							'total_commission_amount'    =>$result['total_commission_amount'],
							'store'                      =>$result['store_id']
							
						);
										
					}

            

					$data['sub_total']               =$sub_total;
                    $data['cgst_order_amount']       =$cgst_order_amount;
					$data['sgst_order_amount']       =$sgst_order_amount;
					$data['sgst_delivery_amount']    =$sgst_delivery_amount;
					$data['cgst_delivery_amount']    =$cgst_delivery_amount;
					$data['commission_amount']       =$commission_amount;
					$data['cgst_commission_amount']  =$cgst_commission_amount;
					$data['sgst_commission_amount']  =$sgst_commission_amount;
					$data['total_commission_amount'] =$total_commission_amount;
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
						$results2=$this->model_extension_report_commission_percentage->getCommissionbystore($filter_data ,$storeid);
                  	
					}
					}

	         if(count($results2)>0)
			{
						$sub_total               =0;
						$cgst_order_amount       =0;
						$sgst_order_amount       =0;
						$cgst_delivery_amount    =0;
						$sgst_delivery_amount    =0;
						$commission_amount       =0;
						$cgst_commission_amount  =0;
						$sgst_commission_amount  =0;
						$store_id                =0;
						$total_commission_amount =0;
						
			foreach ($results2 as $result)
			 {

				$invoiceprefix=$result['invoice_prefix'].$result['invoice_no'];
				$sub_total               += $result['sub_total'];
				$cgst_order_amount       += $result['cgst_order_amount'];
				$sgst_order_amount       += $result['sgst_order_amount'];
				$cgst_delivery_amount    += $result['cgst_delivery_amount'];
				$sgst_delivery_amount    += $result['sgst_delivery_amount'];
				$commission_amount       += $result['commission_amount'];
				$cgst_commission_amount  += $result['cgst_commission_amount'];
				$sgst_commission_amount  += $result['sgst_commission_amount'];
				$total_commission_amount += $result['total_commission_amount'];
				
				$usercount+=1;		
						$data['orders'][]=array(
							'orderid'                    =>$result['order_id'],		
							'date_start'                 =>date($this->language->get('date_format_short'), strtotime($result['date_start'])),	
							'invoice'                    =>$invoiceprefix,
							'sub_total'                  =>$result['sub_total'],
							'payment_method'             =>$result['Mode_of_payment'],	
							'cgst_order_amount'          =>$result['cgst_order_amount'],
							'sgst_order_amount'          =>$result['sgst_order_amount'],
							'delivery_charge'            =>$result['delivery_charge'],
							'cgst_delivery_percentage'   =>$result['cgst_delivery_percentage'],
							'cgst_delivery_amount'       =>$result['cgst_delivery_amount'],
							'sgst_delivery_percentage'   =>$result['sgst_delivery_percentage'],
							'sgst_delivery_amount'       =>$result['sgst_delivery_amount'],
							'commission_percentage'      =>$result['commission_percentage'],
							'commission_amount'          =>$result['commission_amount'],
							'cgst_commission_percentage' =>$result['cgst_commission_percentage'],
							'cgst_commission_amount'     =>$result['cgst_commission_amount'],
							'sgst_commission_percentage' =>$result['sgst_commission_percentage'],
							'sgst_commission_amount'     =>$result['sgst_commission_amount'],
							'total_commission_amount'    =>$result['total_commission_amount'],
							'STORE'                      =>$result['store_id']
							
				);
					}

					$data['sub_total']               =$sub_total;
                    $data['cgst_order_amount']       =$cgst_order_amount;
					$data['sgst_order_amount']       =$sgst_order_amount;
					$data['sgst_delivery_amount']    =$sgst_delivery_amount;
					$data['cgst_delivery_amount']    =$cgst_delivery_amount;
					$data['commission_amount']       =$commission_amount;
					$data['cgst_commission_amount']  =$cgst_commission_amount;
					$data['sgst_commission_amount']  =$sgst_commission_amount;
					$data['total_commission_amount'] =$total_commission_amount;

				
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
		$pagination->url = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=commission_percentage' . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_group'] = $filter_group;
		$data['filter_order_status_id'] = $filter_order_status_id;

		return $this->load->view('extension/report/commission_percentage_info', $data);
	}
}