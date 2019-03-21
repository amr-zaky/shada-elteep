<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Order_details extends API
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_api_order_details');
	}

	/**
	 * @api {get} /order_details/all Get all order_detailss.
	 * @apiVersion 0.1.0
	 * @apiName AllOrderdetails 
	 * @apiGroup order_details
	 * @apiHeader {String} X-Api-Key Order detailss unique access-key.
	 * @apiHeader {String} X-Token Order detailss unique token.
	 * @apiPermission Order details Cant be Accessed permission name : api_order_details_all
	 *
	 * @apiParam {String} [Filter=null] Optional filter of Order detailss.
	 * @apiParam {String} [Field="All Field"] Optional field of Order detailss : detail_id, orders_id, pro_id, detail_count, detail_price.
	 * @apiParam {String} [Start=0] Optional start index of Order detailss.
	 * @apiParam {String} [Limit=10] Optional limit data of Order detailss.
	 *
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of order_details.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError NoDataOrder details Order details data is nothing.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function all_get()
	{
		$this->is_allowed('api_order_details_all');

		$filter = $this->get('filter');
		$field = $this->get('field');
		$limit = $this->get('limit') ? $this->get('limit') : $this->limit_page;
		$start = $this->get('start');

		$select_field = ['detail_id', 'orders_id', 'pro_id', 'detail_count', 'detail_price'];
		$order_detailss = $this->model_api_order_details->get($filter, $field, $limit, $start, $select_field);
		$total = $this->model_api_order_details->count_all($filter, $field);

		$data['order_details'] = $order_detailss;
				
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Order details',
			'data'	 	=> $data,
			'total' 	=> $total
		], API::HTTP_OK);
	}

	
	/**
	 * @api {get} /order_details/detail Detail Order details.
	 * @apiVersion 0.1.0
	 * @apiName DetailOrder details
	 * @apiGroup order_details
	 * @apiHeader {String} X-Api-Key Order detailss unique access-key.
	 * @apiHeader {String} X-Token Order detailss unique token.
	 * @apiPermission Order details Cant be Accessed permission name : api_order_details_detail
	 *
	 * @apiParam {Integer} Id Mandatory id of Order detailss.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of order_details.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError Order detailsNotFound Order details data is not found.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function detail_get()
	{
		$this->is_allowed('api_order_details_detail');

		$this->requiredInput(['detail_id']);

		$id = $this->get('detail_id');

		$select_field = ['detail_id', 'orders_id', 'pro_id', 'detail_count', 'detail_price'];
		$data['order_details'] = $this->model_api_order_details->find($id, $select_field);

		if ($data['order_details']) {
			
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Order details',
				'data'	 	=> $data
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Order details not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

	
	/**
	 * @api {post} /order_details/add Add Order details.
	 * @apiVersion 0.1.0
	 * @apiName AddOrder details
	 * @apiGroup order_details
	 * @apiHeader {String} X-Api-Key Order detailss unique access-key.
	 * @apiHeader {String} X-Token Order detailss unique token.
	 * @apiPermission Order details Cant be Accessed permission name : api_order_details_add
	 *
 	 * @apiParam {String} Orders_id Mandatory orders_id of Order detailss. Input Orders Id Max Length : 11. 
	 * @apiParam {String} Pro_id Mandatory pro_id of Order detailss. Input Pro Id Max Length : 11. 
	 * @apiParam {String} Detail_count Mandatory detail_count of Order detailss. Input Detail Count Max Length : 11. 
	 * @apiParam {String} Detail_price Mandatory detail_price of Order detailss.  
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
		$this->is_allowed('api_order_details_add');

		$this->form_validation->set_rules('orders_id', 'Orders Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('pro_id', 'Pro Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('detail_count', 'Detail Count', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('detail_price', 'Detail Price', 'trim|required');
		
		if ($this->form_validation->run()) {

			$save_data = [
				'orders_id' => $this->input->post('orders_id'),
				'pro_id' => $this->input->post('pro_id'),
				'detail_count' => $this->input->post('detail_count'),
				'detail_price' => $this->input->post('detail_price'),
			];
			
			$save_order_details = $this->model_api_order_details->store($save_data);

			if ($save_order_details) {
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
	 * @api {post} /order_details/update Update Order details.
	 * @apiVersion 0.1.0
	 * @apiName UpdateOrder details
	 * @apiGroup order_details
	 * @apiHeader {String} X-Api-Key Order detailss unique access-key.
	 * @apiHeader {String} X-Token Order detailss unique token.
	 * @apiPermission Order details Cant be Accessed permission name : api_order_details_update
	 *
	 * @apiParam {String} Orders_id Mandatory orders_id of Order detailss. Input Orders Id Max Length : 11. 
	 * @apiParam {String} Pro_id Mandatory pro_id of Order detailss. Input Pro Id Max Length : 11. 
	 * @apiParam {String} Detail_count Mandatory detail_count of Order detailss. Input Detail Count Max Length : 11. 
	 * @apiParam {String} Detail_price Mandatory detail_price of Order detailss.  
	 * @apiParam {Integer} detail_id Mandatory detail_id of Order Details.
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
		$this->is_allowed('api_order_details_update');

		
		$this->form_validation->set_rules('orders_id', 'Orders Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('pro_id', 'Pro Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('detail_count', 'Detail Count', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('detail_price', 'Detail Price', 'trim|required');
		
		if ($this->form_validation->run()) {

			$save_data = [
				'orders_id' => $this->input->post('orders_id'),
				'pro_id' => $this->input->post('pro_id'),
				'detail_count' => $this->input->post('detail_count'),
				'detail_price' => $this->input->post('detail_price'),
			];
			
			$save_order_details = $this->model_api_order_details->change($this->post('detail_id'), $save_data);

			if ($save_order_details) {
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
	 * @api {post} /order_details/delete Delete Order details. 
	 * @apiVersion 0.1.0
	 * @apiName DeleteOrder details
	 * @apiGroup order_details
	 * @apiHeader {String} X-Api-Key Order detailss unique access-key.
	 * @apiHeader {String} X-Token Order detailss unique token.
	 	 * @apiPermission Order details Cant be Accessed permission name : api_order_details_delete
	 *
	 * @apiParam {Integer} Id Mandatory id of Order detailss .
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
		$this->is_allowed('api_order_details_delete');

		$order_details = $this->model_api_order_details->find($this->post('detail_id'));

		if (!$order_details) {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Order details not found'
			], API::HTTP_NOT_ACCEPTABLE);
		} else {
			$delete = $this->model_api_order_details->remove($this->post('detail_id'));

			}
		
		if ($delete) {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Order details deleted',
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Order details not delete'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

}

/* End of file Order details.php */
/* Location: ./application/controllers/api/Order details.php */