<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Offers extends API
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_api_offers');
	}

	/**
	 * @api {get} /offers/all Get all offerss.
	 * @apiVersion 0.1.0
	 * @apiName AllOffers 
	 * @apiGroup offers
	 * @apiHeader {String} X-Api-Key Offerss unique access-key.
	 * @apiHeader {String} X-Token Offerss unique token.
	 * @apiPermission Offers Cant be Accessed permission name : api_offers_all
	 *
	 * @apiParam {String} [Filter=null] Optional filter of Offerss.
	 * @apiParam {String} [Field="All Field"] Optional field of Offerss : offer_id, pro_id, end_date.
	 * @apiParam {String} [Start=0] Optional start index of Offerss.
	 * @apiParam {String} [Limit=10] Optional limit data of Offerss.
	 *
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of offers.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError NoDataOffers Offers data is nothing.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function all_get()
	{
		$this->is_allowed('api_offers_all');

		$filter = $this->get('filter');
		$field = $this->get('field');
		$limit = $this->get('limit') ? $this->get('limit') : $this->limit_page;
		$start = $this->get('start');

		$select_field = ['offer_id', 'pro_id', 'end_date'];
		$offerss = $this->model_api_offers->get($filter, $field, $limit, $start, $select_field);
		$total = $this->model_api_offers->count_all($filter, $field);

		$data['offers'] = $offerss;
				
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Offers',
			'data'	 	=> $data,
			'total' 	=> $total
		], API::HTTP_OK);
	}

	
	/**
	 * @api {get} /offers/detail Detail Offers.
	 * @apiVersion 0.1.0
	 * @apiName DetailOffers
	 * @apiGroup offers
	 * @apiHeader {String} X-Api-Key Offerss unique access-key.
	 * @apiHeader {String} X-Token Offerss unique token.
	 * @apiPermission Offers Cant be Accessed permission name : api_offers_detail
	 *
	 * @apiParam {Integer} Id Mandatory id of Offerss.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of offers.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError OffersNotFound Offers data is not found.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function detail_get()
	{
		$this->is_allowed('api_offers_detail');

		$this->requiredInput(['offer_id']);

		$id = $this->get('offer_id');

		$select_field = ['offer_id', 'pro_id', 'end_date'];
		/*$data['offers'] = $this->model_api_offers->find($id, $select_field);*/

		$data['offers'] = $this->model_api_offers->getofferdetails($id);

		if ($data['offers']) {
			
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Offers',
				'data'	 	=> $data
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Offers not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

	
	/**
	 * @api {post} /offers/add Add Offers.
	 * @apiVersion 0.1.0
	 * @apiName AddOffers
	 * @apiGroup offers
	 * @apiHeader {String} X-Api-Key Offerss unique access-key.
	 * @apiHeader {String} X-Token Offerss unique token.
	 * @apiPermission Offers Cant be Accessed permission name : api_offers_add
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
		$this->is_allowed('api_offers_add');

		
		if ($this->form_validation->run()) {

			$save_data = [
			];
			
			$save_offers = $this->model_api_offers->store($save_data);

			if ($save_offers) {
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
	 * @api {post} /offers/update Update Offers.
	 * @apiVersion 0.1.0
	 * @apiName UpdateOffers
	 * @apiGroup offers
	 * @apiHeader {String} X-Api-Key Offerss unique access-key.
	 * @apiHeader {String} X-Token Offerss unique token.
	 * @apiPermission Offers Cant be Accessed permission name : api_offers_update
	 *
	 * @apiParam {Integer} offer_id Mandatory offer_id of Offers.
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
		$this->is_allowed('api_offers_update');

		
		
		if ($this->form_validation->run()) {

			$save_data = [
			];
			
			$save_offers = $this->model_api_offers->change($this->post('offer_id'), $save_data);

			if ($save_offers) {
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
	 * @api {post} /offers/delete Delete Offers. 
	 * @apiVersion 0.1.0
	 * @apiName DeleteOffers
	 * @apiGroup offers
	 * @apiHeader {String} X-Api-Key Offerss unique access-key.
	 * @apiHeader {String} X-Token Offerss unique token.
	 	 * @apiPermission Offers Cant be Accessed permission name : api_offers_delete
	 *
	 * @apiParam {Integer} Id Mandatory id of Offerss .
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
		$this->is_allowed('api_offers_delete');

		$offers = $this->model_api_offers->find($this->post('offer_id'));

		if (!$offers) {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Offers not found'
			], API::HTTP_NOT_ACCEPTABLE);
		} else {
			$delete = $this->model_api_offers->remove($this->post('offer_id'));

			}
		
		if ($delete) {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Offers deleted',
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Offers not delete'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

}

/* End of file Offers.php */
/* Location: ./application/controllers/api/Offers.php */