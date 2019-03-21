<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Customers extends API
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_api_customers');
	}

	/**
	 * @api {get} /customers/all Get all customerss.
	 * @apiVersion 0.1.0
	 * @apiName AllCustomers 
	 * @apiGroup customers
	 * @apiHeader {String} X-Api-Key Customerss unique access-key.
	 * @apiHeader {String} X-Token Customerss unique token.
	 * @apiPermission Customers Cant be Accessed permission name : api_customers_all
	 *
	 * @apiParam {String} [Filter=null] Optional filter of Customerss.
	 * @apiParam {String} [Field="All Field"] Optional field of Customerss : customer_id, first_name, last_name, phone, email, password, image, wallet_credit, verfication_code, is_verified, is_active.
	 * @apiParam {String} [Start=0] Optional start index of Customerss.
	 * @apiParam {String} [Limit=10] Optional limit data of Customerss.
	 *
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of customers.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError NoDataCustomers Customers data is nothing.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */

public function login_post()
	{
		

		$json = file_get_contents('php://input');
        $data=json_decode($json,true);

			$email=$this->input->post('email');
			$password=$this->input->post('password');


			if ($data=$this->aauth->login_member($email, $password, $this->input->post('remember'))) 
			{
				

			if(@$data['msg']['customer_id'])
			{
			    $id=array(
					'id'=>$data['msg']['customer_id']
				);
				$data['msg']['token']=$this->jwtEncode($id);
				
				$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Customers',
			'data'	 	=> $data['msg']
			
			], API::HTTP_OK);
			}
			
			else 
			{
				$this->response([
			'status' 	=> false,
            'error'=>"no matched user",
            'msg'=>$data
			], API::HTTP_NOT_ACCEPTABLE);

			}
			}

			 else {

			$this->response([
			'status' 	=> true,
			'message' 	=> 'TRY AGAIN',
			
			
			], API::HTTP_ERROR);

			}
	
		
		
		
	}







	public function all_get()
	{
		$this->is_allowed('api_customers_all');

		$filter = $this->get('filter');
		$field = $this->get('field');
		$limit = $this->get('limit') ? $this->get('limit') : $this->limit_page;
		$start = $this->get('start');

		$select_field = ['customer_id', 'first_name', 'last_name', 'phone', 'email', 'password', 'image', 'wallet_credit', 'verfication_code', 'is_verified', 'is_active'];
		$customerss = $this->model_api_customers->get($filter, $field, $limit, $start, $select_field);
		$total = $this->model_api_customers->count_all($filter, $field);

		$customers_arr = [];

		foreach ($customerss as $customers) {
			$customers->image  = BASE_URL.'uploads/customers/'.$customers->image;
			$customers_arr[] = $customers;
		}

		$data['customers'] = $customers_arr;
				
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Customers',
			'data'	 	=> $data,
			'total' 	=> $total
		], API::HTTP_OK);
	}

	
	/**
	 * @api {get} /customers/detail Detail Customers.
	 * @apiVersion 0.1.0
	 * @apiName DetailCustomers
	 * @apiGroup customers
	 * @apiHeader {String} X-Api-Key Customerss unique access-key.
	 * @apiHeader {String} X-Token Customerss unique token.
	 * @apiPermission Customers Cant be Accessed permission name : api_customers_detail
	 *
	 * @apiParam {Integer} Id Mandatory id of Customerss.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of customers.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError CustomersNotFound Customers data is not found.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function detail_get()
	{
		$this->is_allowed('api_customers_detail');

		$this->requiredInput(['customer_id']);

		$id = $this->get('customer_id');

		$select_field = ['customer_id', 'first_name', 'last_name', 'phone', 'email', 'password', 'image', 'wallet_credit', 'verfication_code', 'is_verified', 'is_active'];
		$data['customers'] = $this->model_api_customers->find($id, $select_field);

		if ($data['customers']) {
			$data['customers']->image = BASE_URL.'uploads/customers/'.$data['customers']->image;
			
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Customers',
				'data'	 	=> $data
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Customers not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

	
	/**
	 * @api {post} /customers/add Add Customers.
	 * @apiVersion 0.1.0
	 * @apiName AddCustomers
	 * @apiGroup customers
	 * @apiHeader {String} X-Api-Key Customerss unique access-key.
	 * @apiHeader {String} X-Token Customerss unique token.
	 * @apiPermission Customers Cant be Accessed permission name : api_customers_add
	 *
 	 * @apiParam {String} First_name Mandatory first_name of Customerss. Input First Name Max Length : 100. 
	 * @apiParam {String} Last_name Mandatory last_name of Customerss. Input Last Name Max Length : 100. 
	 * @apiParam {String} Phone Mandatory phone of Customerss. Input Phone Max Length : 100. 
	 * @apiParam {String} Email Mandatory email of Customerss.  
	 * @apiParam {String} Password Mandatory password of Customerss.  
	 * @apiParam {File} Image Mandatory image of Customerss.  
	 * @apiParam {String} [Wallet_credit] Optional wallet_credit of Customerss.  
	 * @apiParam {String} [Verfication_code] Optional verfication_code of Customerss.  
	 * @apiParam {String} [Is_verified] Optional is_verified of Customerss.  
	 * @apiParam {String} [Is_active] Optional is_active of Customerss.  
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError ValidationError Error validation.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function add_post()
	{
		$this->is_allowed('api_customers_add');

		$this->form_validation->set_rules('first_name', 'First Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		
		if ($this->form_validation->run()) {

			$save_data = [
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'phone' => $this->input->post('phone'),
				'email' => $this->input->post('email'),
				'password' => $this->input->post('password'),
				'wallet_credit' =>0,
				'verfication_code' =>0,
				'is_verified' =>0,
				'is_active' =>0,
			];
			if (!is_dir(FCPATH . '/uploads/customers')) {
				mkdir(FCPATH . '/uploads/customers');
			}
			
			$config = [
				'upload_path' 	=> './uploads/customers/',
					'required' 		=> true
			];
			
			if ($upload = $this->upload_file('image', $config)){
				$upload_data = $this->upload->data();
				$save_data['image'] = $upload['file_name'];
			}

			$save_customers = $this->model_api_customers->store($save_data);

			if ($save_customers) {
				$this->response([
					'status' 	=> true,
					'message' 	=> 'Your data has been successfully stored into the database'
				], API::HTTP_OK);

			} else {
				$this->response([
					'status' 	=> false,
					'message' 	=> cclang('data_not_change')
				], API::HTTP_NOT_ACCEPTABLE);
			}

		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> validation_errors()
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

	/**
	 * @api {post} /customers/update Update Customers.
	 * @apiVersion 0.1.0
	 * @apiName UpdateCustomers
	 * @apiGroup customers
	 * @apiHeader {String} X-Api-Key Customerss unique access-key.
	 * @apiHeader {String} X-Token Customerss unique token.
	 * @apiPermission Customers Cant be Accessed permission name : api_customers_update
	 *
	 * @apiParam {String} First_name Mandatory first_name of Customerss. Input First Name Max Length : 100. 
	 * @apiParam {String} Last_name Mandatory last_name of Customerss. Input Last Name Max Length : 100. 
	 * @apiParam {String} Phone Mandatory phone of Customerss. Input Phone Max Length : 100. 
	 * @apiParam {String} Email Mandatory email of Customerss.  
	 * @apiParam {String} Password Mandatory password of Customerss.  
	 * @apiParam {File} Image Mandatory image of Customerss.  
	 * @apiParam {String} [Wallet_credit] Optional wallet_credit of Customerss.  
	 * @apiParam {String} [Verfication_code] Optional verfication_code of Customerss.  
	 * @apiParam {String} [Is_verified] Optional is_verified of Customerss.  
	 * @apiParam {String} [Is_active] Optional is_active of Customerss.  
	 * @apiParam {Integer} customer_id Mandatory customer_id of Customers.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError ValidationError Error validation.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function update_post()
	{
		$this->is_allowed('api_customers_update');

		
		$this->form_validation->set_rules('first_name', 'First Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		
		if ($this->form_validation->run()) {

			$save_data = [
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'phone' => $this->input->post('phone'),
				'email' => $this->input->post('email'),
				'password' => $this->input->post('password'),
				'wallet_credit' => $this->input->post('wallet_credit'),
				'verfication_code' => $this->input->post('verfication_code'),
				'is_verified' => $this->input->post('is_verified'),
				'is_active' => $this->input->post('is_active'),
			];
			if (!is_dir(FCPATH . '/uploads/customers')) {
				mkdir(FCPATH . '/uploads/customers');
			}
			
			$config = [
				'upload_path' 	=> './uploads/customers/',
					'required' 		=> true
			];
			
			if ($upload = $this->upload_file('image', $config)){
				$upload_data = $this->upload->data();
				$save_data['image'] = $upload['file_name'];
			}

			$save_customers = $this->model_api_customers->change($this->post('customer_id'), $save_data);

			if ($save_customers) {
				$this->response([
					'status' 	=> true,
					'message' 	=> 'Your data has been successfully updated into the database'
				], API::HTTP_OK);

			} else {
				$this->response([
					'status' 	=> false,
					'message' 	=> cclang('data_not_change')
				], API::HTTP_NOT_ACCEPTABLE);
			}

		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> validation_errors()
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}
	
	/**
	 * @api {post} /customers/delete Delete Customers. 
	 * @apiVersion 0.1.0
	 * @apiName DeleteCustomers
	 * @apiGroup customers
	 * @apiHeader {String} X-Api-Key Customerss unique access-key.
	 * @apiHeader {String} X-Token Customerss unique token.
	 	 * @apiPermission Customers Cant be Accessed permission name : api_customers_delete
	 *
	 * @apiParam {Integer} Id Mandatory id of Customerss .
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError ValidationError Error validation.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function delete_post()
	{
		$this->is_allowed('api_customers_delete');

		$customers = $this->model_api_customers->find($this->post('customer_id'));

		if (!$customers) {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Customers not found'
			], API::HTTP_NOT_ACCEPTABLE);
		} else {
			$delete = $this->model_api_customers->remove($this->post('customer_id'));

			if (!empty($customers->image)) {
				$path = FCPATH . '/uploads/customers/' . $customers->image;

				if (is_file($path)) {
					$delete_file = unlink($path);
				}
			}

		}
		
		if ($delete) {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Customers deleted',
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Customers not delete'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

}

/* End of file Customers.php */
/* Location: ./application/controllers/api/Customers.php */