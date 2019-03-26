<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Sitting extends API
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_api_sitting');
	}

	/**
	 * @api {get} /sitting/all Get all sittings.
	 * @apiVersion 0.1.0
	 * @apiName AllSitting 
	 * @apiGroup sitting
	 * @apiHeader {String} X-Api-Key Sittings unique access-key.
	 * @apiHeader {String} X-Token Sittings unique token.
	 * @apiPermission Sitting Cant be Accessed permission name : api_sitting_all
	 *
	 * @apiParam {String} [Filter=null] Optional filter of Sittings.
	 * @apiParam {String} [Field="All Field"] Optional field of Sittings : id, page_name, content.
	 * @apiParam {String} [Start=0] Optional start index of Sittings.
	 * @apiParam {String} [Limit=10] Optional limit data of Sittings.
	 *
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of sitting.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError NoDataSitting Sitting data is nothing.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function all_get()
	{
		$this->is_allowed('api_sitting_all');

		$filter = $this->get('filter');
		$field = $this->get('field');
		$limit = $this->get('limit') ? $this->get('limit') : $this->limit_page;
		$start = $this->get('start');

		$select_field = ['id', 'page_name', 'content'];
		$sittings = $this->model_api_sitting->get($filter, $field, $limit, $start, $select_field);
		$total = $this->model_api_sitting->count_all($filter, $field);

		$data['sitting'] = $sittings;
				
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Sitting',
			'data'	 	=> $data,
			'total' 	=> $total
		], API::HTTP_OK);
	}

	
	/**
	 * @api {get} /sitting/detail Detail Sitting.
	 * @apiVersion 0.1.0
	 * @apiName DetailSitting
	 * @apiGroup sitting
	 * @apiHeader {String} X-Api-Key Sittings unique access-key.
	 * @apiHeader {String} X-Token Sittings unique token.
	 * @apiPermission Sitting Cant be Accessed permission name : api_sitting_detail
	 *
	 * @apiParam {Integer} Id Mandatory id of Sittings.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of sitting.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError SittingNotFound Sitting data is not found.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function detail_get()
	{
		$this->is_allowed('api_sitting_detail');

		$this->requiredInput(['id']);

		$id = $this->get('id');

		$select_field = ['id', 'page_name', 'content'];
		$data['sitting'] = $this->model_api_sitting->find($id, $select_field);

		if ($data['sitting']) {
			
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Sitting',
				'data'	 	=> $data
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Sitting not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

	
	/**
	 * @api {post} /sitting/add Add Sitting.
	 * @apiVersion 0.1.0
	 * @apiName AddSitting
	 * @apiGroup sitting
	 * @apiHeader {String} X-Api-Key Sittings unique access-key.
	 * @apiHeader {String} X-Token Sittings unique token.
	 * @apiPermission Sitting Cant be Accessed permission name : api_sitting_add
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
		$this->is_allowed('api_sitting_add');

		
		if ($this->form_validation->run()) {

			$save_data = [
			];
			
			$save_sitting = $this->model_api_sitting->store($save_data);

			if ($save_sitting) {
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
	 * @api {post} /sitting/update Update Sitting.
	 * @apiVersion 0.1.0
	 * @apiName UpdateSitting
	 * @apiGroup sitting
	 * @apiHeader {String} X-Api-Key Sittings unique access-key.
	 * @apiHeader {String} X-Token Sittings unique token.
	 * @apiPermission Sitting Cant be Accessed permission name : api_sitting_update
	 *
	 * @apiParam {Integer} id Mandatory id of Sitting.
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
		$this->is_allowed('api_sitting_update');

		
		
		if ($this->form_validation->run()) {

			$save_data = [
			];
			
			$save_sitting = $this->model_api_sitting->change($this->post('id'), $save_data);

			if ($save_sitting) {
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
	 * @api {post} /sitting/delete Delete Sitting. 
	 * @apiVersion 0.1.0
	 * @apiName DeleteSitting
	 * @apiGroup sitting
	 * @apiHeader {String} X-Api-Key Sittings unique access-key.
	 * @apiHeader {String} X-Token Sittings unique token.
	 	 * @apiPermission Sitting Cant be Accessed permission name : api_sitting_delete
	 *
	 * @apiParam {Integer} Id Mandatory id of Sittings .
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
		$this->is_allowed('api_sitting_delete');

		$sitting = $this->model_api_sitting->find($this->post('id'));

		if (!$sitting) {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Sitting not found'
			], API::HTTP_NOT_ACCEPTABLE);
		} else {
			$delete = $this->model_api_sitting->remove($this->post('id'));

			}
		
		if ($delete) {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Sitting deleted',
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Sitting not delete'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

}

/* End of file Sitting.php */
/* Location: ./application/controllers/api/Sitting.php */