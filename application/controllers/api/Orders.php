<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Orders extends API
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_api_orders');
	}

	/**
	 * @api {get} /orders/all Get all orderss.
	 * @apiVersion 0.1.0
	 * @apiName AllOrders 
	 * @apiGroup orders
	 * @apiHeader {String} X-Api-Key Orderss unique access-key.
	 * @apiHeader {String} X-Token Orderss unique token.
	 * @apiPermission Orders Cant be Accessed permission name : api_orders_all
	 *
	 * @apiParam {String} [Filter=null] Optional filter of Orderss.
	 * @apiParam {String} [Field="All Field"] Optional field of Orderss : order_id, cust_id, area_id, order_status.
	 * @apiParam {String} [Start=0] Optional start index of Orderss.
	 * @apiParam {String} [Limit=10] Optional limit data of Orderss.
	 *
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of orders.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError NoDataOrders Orders data is nothing.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function all_get()
	{
		$this->is_allowed('api_orders_all');

		$filter = $this->get('filter');
		$field = $this->get('field');
		$limit = $this->get('limit') ? $this->get('limit') : $this->limit_page;
		$start = $this->get('start');

		$select_field = ['order_id', 'cust_id', 'area_id', 'order_status'];
		$orderss = $this->model_api_orders->get($filter, $field, $limit, $start, $select_field);
		$total = $this->model_api_orders->count_all($filter, $field);

		$data['orders'] = $orderss;
				
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Orders',
			'data'	 	=> $data,
			'total' 	=> $total
		], API::HTTP_OK);
	}

	
	/**
	 * @api {get} /orders/detail Detail Orders.
	 * @apiVersion 0.1.0
	 * @apiName DetailOrders
	 * @apiGroup orders
	 * @apiHeader {String} X-Api-Key Orderss unique access-key.
	 * @apiHeader {String} X-Token Orderss unique token.
	 * @apiPermission Orders Cant be Accessed permission name : api_orders_detail
	 *
	 * @apiParam {Integer} Id Mandatory id of Orderss.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of orders.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError OrdersNotFound Orders data is not found.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function detail_get()
	{
		$this->is_allowed('api_orders_detail');

		$this->requiredInput(['order_id']);

		$id = $this->get('order_id');

		$select_field = ['order_id', 'cust_id', 'area_id', 'order_status'];
		$data['orders'] = $this->model_api_orders->find($id, $select_field);

		if ($data['orders']) {
			
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Orders',
				'data'	 	=> $data
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Orders not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

	
	/**
	 * @api {post} /orders/add Add Orders.
	 * @apiVersion 0.1.0
	 * @apiName AddOrders
	 * @apiGroup orders
	 * @apiHeader {String} X-Api-Key Orderss unique access-key.
	 * @apiHeader {String} X-Token Orderss unique token.
	 * @apiPermission Orders Cant be Accessed permission name : api_orders_add
	 *
 	 * @apiParam {String} Cust_id Mandatory cust_id of Orderss. Input Cust Id Max Length : 11. 
	 * @apiParam {String} Area_id Mandatory area_id of Orderss. Input Area Id Max Length : 11. 
	 * @apiParam {String} Order_status Mandatory order_status of Orderss. Input Order Status Max Length : 11. 
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
		$this->is_allowed('api_orders_add');

		$this->form_validation->set_rules('cust_id', 'Cust Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('area_id', 'Area Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('order_status', 'Order Status', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {

			$save_data = [
				'cust_id' => $this->input->post('cust_id'),
				'area_id' => $this->input->post('area_id'),
				'order_status' => $this->input->post('order_status'),
			];
			
			$save_orders = $this->model_api_orders->store($save_data);

			if ($save_orders) {
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
	 * @api {post} /orders/update Update Orders.
	 * @apiVersion 0.1.0
	 * @apiName UpdateOrders
	 * @apiGroup orders
	 * @apiHeader {String} X-Api-Key Orderss unique access-key.
	 * @apiHeader {String} X-Token Orderss unique token.
	 * @apiPermission Orders Cant be Accessed permission name : api_orders_update
	 *
	 * @apiParam {String} Area_id Mandatory area_id of Orderss. Input Area Id Max Length : 11. 
	 * @apiParam {String} Order_status Mandatory order_status of Orderss. Input Order Status Max Length : 11. 
	 * @apiParam {Integer} order_id Mandatory order_id of Orders.
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
		$this->is_allowed('api_orders_update');

		
		$this->form_validation->set_rules('area_id', 'Area Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('order_status', 'Order Status', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {

			$save_data = [
				'area_id' => $this->input->post('area_id'),
				'order_status' => $this->input->post('order_status'),
			];
			
			$save_orders = $this->model_api_orders->change($this->post('order_id'), $save_data);

			if ($save_orders) {
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
	 * @api {post} /orders/delete Delete Orders. 
	 * @apiVersion 0.1.0
	 * @apiName DeleteOrders
	 * @apiGroup orders
	 * @apiHeader {String} X-Api-Key Orderss unique access-key.
	 * @apiHeader {String} X-Token Orderss unique token.
	 	 * @apiPermission Orders Cant be Accessed permission name : api_orders_delete
	 *
	 * @apiParam {Integer} Id Mandatory id of Orderss .
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
		$this->is_allowed('api_orders_delete');

		$orders = $this->model_api_orders->find($this->post('order_id'));

		if (!$orders) {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Orders not found'
			], API::HTTP_NOT_ACCEPTABLE);
		} else {
			$delete = $this->model_api_orders->remove($this->post('order_id'));

			}
		
		if ($delete) {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Orders deleted',
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Orders not delete'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

}

/* End of file Orders.php */
/* Location: ./application/controllers/api/Orders.php */