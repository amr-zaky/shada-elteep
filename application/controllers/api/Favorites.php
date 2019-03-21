<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Favorites extends API
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_api_favorites');
	}

	/**
	 * @api {get} /favorites/all Get all favoritess.
	 * @apiVersion 0.1.0
	 * @apiName AllFavorites 
	 * @apiGroup favorites
	 * @apiHeader {String} X-Api-Key Favoritess unique access-key.
	 * @apiHeader {String} X-Token Favoritess unique token.
	 * @apiPermission Favorites Cant be Accessed permission name : api_favorites_all
	 *
	 * @apiParam {String} [Filter=null] Optional filter of Favoritess.
	 * @apiParam {String} [Field="All Field"] Optional field of Favoritess : favorite_id, pro_id, cust_id.
	 * @apiParam {String} [Start=0] Optional start index of Favoritess.
	 * @apiParam {String} [Limit=10] Optional limit data of Favoritess.
	 *
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of favorites.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError NoDataFavorites Favorites data is nothing.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function all_get()
	{
		$this->is_allowed('api_favorites_all');

		$filter = $this->get('filter');
		$field = $this->get('field');
		$limit = $this->get('limit') ? $this->get('limit') : $this->limit_page;
		$start = $this->get('start');

		$select_field = ['favorite_id', 'pro_id', 'cust_id'];
		$favoritess = $this->model_api_favorites->get($filter, $field, $limit, $start, $select_field);
		$total = $this->model_api_favorites->count_all($filter, $field);

		$data['favorites'] = $favoritess;
				
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Favorites',
			'data'	 	=> $data,
			'total' 	=> $total
		], API::HTTP_OK);
	}

	
	/**
	 * @api {get} /favorites/detail Detail Favorites.
	 * @apiVersion 0.1.0
	 * @apiName DetailFavorites
	 * @apiGroup favorites
	 * @apiHeader {String} X-Api-Key Favoritess unique access-key.
	 * @apiHeader {String} X-Token Favoritess unique token.
	 * @apiPermission Favorites Cant be Accessed permission name : api_favorites_detail
	 *
	 * @apiParam {Integer} Id Mandatory id of Favoritess.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of favorites.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError FavoritesNotFound Favorites data is not found.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function detail_get()
	{
		$this->is_allowed('api_favorites_detail');

		$this->requiredInput(['favorite_id']);

		$id = $this->get('favorite_id');

		$select_field = ['favorite_id', 'pro_id', 'cust_id'];
		$data['favorites'] = $this->model_api_favorites->find($id, $select_field);

		if ($data['favorites']) {
			
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Favorites',
				'data'	 	=> $data
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Favorites not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

	
	/**
	 * @api {post} /favorites/add Add Favorites.
	 * @apiVersion 0.1.0
	 * @apiName AddFavorites
	 * @apiGroup favorites
	 * @apiHeader {String} X-Api-Key Favoritess unique access-key.
	 * @apiHeader {String} X-Token Favoritess unique token.
	 * @apiPermission Favorites Cant be Accessed permission name : api_favorites_add
	 *
 	 * @apiParam {String} Pro_id Mandatory pro_id of Favoritess. Input Pro Id Max Length : 11. 
	 * @apiParam {String} Cust_id Mandatory cust_id of Favoritess. Input Cust Id Max Length : 11. 
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
		$this->is_allowed('api_favorites_add');

		$this->form_validation->set_rules('pro_id', 'Pro Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('cust_id', 'Cust Id', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {

			$save_data = [
				'pro_id' => $this->input->post('pro_id'),
				'cust_id' => $this->input->post('cust_id'),
			];
			
			$save_favorites = $this->model_api_favorites->store($save_data);

			if ($save_favorites) {
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
	 * @api {post} /favorites/update Update Favorites.
	 * @apiVersion 0.1.0
	 * @apiName UpdateFavorites
	 * @apiGroup favorites
	 * @apiHeader {String} X-Api-Key Favoritess unique access-key.
	 * @apiHeader {String} X-Token Favoritess unique token.
	 * @apiPermission Favorites Cant be Accessed permission name : api_favorites_update
	 *
	 * @apiParam {Integer} favorite_id Mandatory favorite_id of Favorites.
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
		$this->is_allowed('api_favorites_update');

		
		
		if ($this->form_validation->run()) {

			$save_data = [
			];
			
			$save_favorites = $this->model_api_favorites->change($this->post('favorite_id'), $save_data);

			if ($save_favorites) {
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
	 * @api {post} /favorites/delete Delete Favorites. 
	 * @apiVersion 0.1.0
	 * @apiName DeleteFavorites
	 * @apiGroup favorites
	 * @apiHeader {String} X-Api-Key Favoritess unique access-key.
	 * @apiHeader {String} X-Token Favoritess unique token.
	 	 * @apiPermission Favorites Cant be Accessed permission name : api_favorites_delete
	 *
	 * @apiParam {Integer} Id Mandatory id of Favoritess .
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
		$this->is_allowed('api_favorites_delete');

		$favorites = $this->model_api_favorites->find($this->post('favorite_id'));

		if (!$favorites) {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Favorites not found'
			], API::HTTP_NOT_ACCEPTABLE);
		} else {
			$delete = $this->model_api_favorites->remove($this->post('favorite_id'));

			}
		
		if ($delete) {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Favorites deleted',
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Favorites not delete'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

}

/* End of file Favorites.php */
/* Location: ./application/controllers/api/Favorites.php */