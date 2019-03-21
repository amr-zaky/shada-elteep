<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Categories extends API
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_api_categories');
	}

	/**
	 * @api {get} /categories/all Get all categoriess.
	 * @apiVersion 0.1.0
	 * @apiName AllCategories 
	 * @apiGroup categories
	 * @apiHeader {String} X-Api-Key Categoriess unique access-key.
	 * @apiHeader {String} X-Token Categoriess unique token.
	 * @apiPermission Categories Cant be Accessed permission name : api_categories_all
	 *
	 * @apiParam {String} [Filter=null] Optional filter of Categoriess.
	 * @apiParam {String} [Field="All Field"] Optional field of Categoriess : category_id, category_name.
	 * @apiParam {String} [Start=0] Optional start index of Categoriess.
	 * @apiParam {String} [Limit=10] Optional limit data of Categoriess.
	 *
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of categories.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError NoDataCategories Categories data is nothing.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function all_get()
	{
		$this->is_allowed('api_categories_all');

		$filter = $this->get('filter');
		$field = $this->get('field');
		$limit = $this->get('limit') ? $this->get('limit') : $this->limit_page;
		$start = $this->get('start');

		$select_field = ['category_id', 'category_name'];
		$categoriess = $this->model_api_categories->get($filter, $field, $limit, $start, $select_field);
		$total = $this->model_api_categories->count_all($filter, $field);

		$data['categories'] = $categoriess;
				
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Categories',
			'data'	 	=> $data,
			'total' 	=> $total
		], API::HTTP_OK);
	}

	
	/**
	 * @api {get} /categories/detail Detail Categories.
	 * @apiVersion 0.1.0
	 * @apiName DetailCategories
	 * @apiGroup categories
	 * @apiHeader {String} X-Api-Key Categoriess unique access-key.
	 * @apiHeader {String} X-Token Categoriess unique token.
	 * @apiPermission Categories Cant be Accessed permission name : api_categories_detail
	 *
	 * @apiParam {Integer} Id Mandatory id of Categoriess.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of categories.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError CategoriesNotFound Categories data is not found.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function detail_get()
	{
		$this->is_allowed('api_categories_detail');

		$this->requiredInput(['category_id']);

		$id = $this->get('category_id');

		$select_field = ['category_id', 'category_name'];
		$data['categories'] = $this->model_api_categories->find($id, $select_field);

		if ($data['categories']) {
			
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Categories',
				'data'	 	=> $data
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Categories not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

	
	/**
	 * @api {post} /categories/add Add Categories.
	 * @apiVersion 0.1.0
	 * @apiName AddCategories
	 * @apiGroup categories
	 * @apiHeader {String} X-Api-Key Categoriess unique access-key.
	 * @apiHeader {String} X-Token Categoriess unique token.
	 * @apiPermission Categories Cant be Accessed permission name : api_categories_add
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
		$this->is_allowed('api_categories_add');

		
		if ($this->form_validation->run()) {

			$save_data = [
			];
			
			$save_categories = $this->model_api_categories->store($save_data);

			if ($save_categories) {
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
	 * @api {post} /categories/update Update Categories.
	 * @apiVersion 0.1.0
	 * @apiName UpdateCategories
	 * @apiGroup categories
	 * @apiHeader {String} X-Api-Key Categoriess unique access-key.
	 * @apiHeader {String} X-Token Categoriess unique token.
	 * @apiPermission Categories Cant be Accessed permission name : api_categories_update
	 *
	 * @apiParam {Integer} category_id Mandatory category_id of Categories.
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
		$this->is_allowed('api_categories_update');

		
		
		if ($this->form_validation->run()) {

			$save_data = [
			];
			
			$save_categories = $this->model_api_categories->change($this->post('category_id'), $save_data);

			if ($save_categories) {
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
	 * @api {post} /categories/delete Delete Categories. 
	 * @apiVersion 0.1.0
	 * @apiName DeleteCategories
	 * @apiGroup categories
	 * @apiHeader {String} X-Api-Key Categoriess unique access-key.
	 * @apiHeader {String} X-Token Categoriess unique token.
	 	 * @apiPermission Categories Cant be Accessed permission name : api_categories_delete
	 *
	 * @apiParam {Integer} Id Mandatory id of Categoriess .
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
		$this->is_allowed('api_categories_delete');

		$categories = $this->model_api_categories->find($this->post('category_id'));

		if (!$categories) {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Categories not found'
			], API::HTTP_NOT_ACCEPTABLE);
		} else {
			$delete = $this->model_api_categories->remove($this->post('category_id'));

			}
		
		if ($delete) {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Categories deleted',
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Categories not delete'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

}

/* End of file Categories.php */
/* Location: ./application/controllers/api/Categories.php */