<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Products extends API
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_api_products');
	}

	/**
	 * @api {get} /products/all Get all productss.
	 * @apiVersion 0.1.0
	 * @apiName AllProducts 
	 * @apiGroup products
	 * @apiHeader {String} X-Api-Key Productss unique access-key.
	 * @apiHeader {String} X-Token Productss unique token.
	 * @apiPermission Products Cant be Accessed permission name : api_products_all
	 *
	 * @apiParam {String} [Filter=null] Optional filter of Productss.
	 * @apiParam {String} [Field="All Field"] Optional field of Productss : product_id, product_name, product_count, product_price, status, cat_id, product_image, description, barcode, product_price_offer.
	 * @apiParam {String} [Start=0] Optional start index of Productss.
	 * @apiParam {String} [Limit=10] Optional limit data of Productss.
	 *
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of products.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError NoDataProducts Products data is nothing.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function all_get()
	{
		$this->is_allowed('api_products_all');

		$filter = $this->get('filter');
		$field = $this->get('field');
		$limit = $this->get('limit') ? $this->get('limit') : $this->limit_page;
		$start = $this->get('start');

		$select_field = ['product_id', 'product_name', 'product_count', 'product_price', 'status', 'cat_id', 'product_image', 'description', 'barcode', 'product_price_offer'];
		$productss = $this->model_api_products->get($filter, $field, $limit, $start, $select_field);
		$total = $this->model_api_products->count_all($filter, $field);

		$data['products'] = $productss;
				
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Products',
			'data'	 	=> $data,
			'total' 	=> $total
		], API::HTTP_OK);
	}

	
	/**
	 * @api {get} /products/detail Detail Products.
	 * @apiVersion 0.1.0
	 * @apiName DetailProducts
	 * @apiGroup products
	 * @apiHeader {String} X-Api-Key Productss unique access-key.
	 * @apiHeader {String} X-Token Productss unique token.
	 * @apiPermission Products Cant be Accessed permission name : api_products_detail
	 *
	 * @apiParam {Integer} Id Mandatory id of Productss.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of products.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError ProductsNotFound Products data is not found.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function detail_get()
	{
		$this->is_allowed('api_products_detail');

		$this->requiredInput(['product_id']);
		$this->requiredInput(['cust_id']);

		$id = $this->get('product_id');
		$cust_id= $this->get('cust_id');
		$select_field = ['product_id', 'product_name', 'product_count', 'product_price', 'status', 'cat_id', 'product_image', 'description', 'barcode', 'product_price_offer'];
		$data['products'] = $this->model_api_products->find($id, $select_field);

		$test['favorites'] = $this->model_api_products->getcustomprodact($id,$cust_id);


		if ($data['products']) {
			
			 if(@$test['favorites'])
			 {

			 		$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Products',
				'data'	 	=> $data ,
				'favorites'=>true
			], API::HTTP_OK);

			 }

			 else 
			 {
			 	$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Products',
				'data'	 	=> $data ,
				'favorites'=>false
			], API::HTTP_OK);

			 }
			
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Products not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}



	

	
	/**
	 * @api {post} /products/add Add Products.
	 * @apiVersion 0.1.0
	 * @apiName AddProducts
	 * @apiGroup products
	 * @apiHeader {String} X-Api-Key Productss unique access-key.
	 * @apiHeader {String} X-Token Productss unique token.
	 * @apiPermission Products Cant be Accessed permission name : api_products_add
	 *
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
		$this->is_allowed('api_products_add');

		
		if ($this->form_validation->run()) {

			$save_data = [
			];
			
			$save_products = $this->model_api_products->store($save_data);

			if ($save_products) {
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
	 * @api {post} /products/update Update Products.
	 * @apiVersion 0.1.0
	 * @apiName UpdateProducts
	 * @apiGroup products
	 * @apiHeader {String} X-Api-Key Productss unique access-key.
	 * @apiHeader {String} X-Token Productss unique token.
	 * @apiPermission Products Cant be Accessed permission name : api_products_update
	 *
	 * @apiParam {Integer} product_id Mandatory product_id of Products.
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
		$this->is_allowed('api_products_update');

		
		
		if ($this->form_validation->run()) {

			$save_data = [
			];
			
			$save_products = $this->model_api_products->change($this->post('product_id'), $save_data);

			if ($save_products) {
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
	 * @api {post} /products/delete Delete Products. 
	 * @apiVersion 0.1.0
	 * @apiName DeleteProducts
	 * @apiGroup products
	 * @apiHeader {String} X-Api-Key Productss unique access-key.
	 * @apiHeader {String} X-Token Productss unique token.
	 	 * @apiPermission Products Cant be Accessed permission name : api_products_delete
	 *
	 * @apiParam {Integer} Id Mandatory id of Productss .
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
		$this->is_allowed('api_products_delete');

		$products = $this->model_api_products->find($this->post('product_id'));

		if (!$products) {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Products not found'
			], API::HTTP_NOT_ACCEPTABLE);
		} else {
			$delete = $this->model_api_products->remove($this->post('product_id'));

			}
		
		if ($delete) {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Products deleted',
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Products not delete'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

}

/* End of file Products.php */
/* Location: ./application/controllers/api/Products.php */