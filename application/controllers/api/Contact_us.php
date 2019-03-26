<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Contact_us extends API
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_api_contact_us');
	}

	/**
	 * @api {get} /contact_us/all Get all contact_uss.
	 * @apiVersion 0.1.0
	 * @apiName AllContactus 
	 * @apiGroup contact_us
	 * @apiHeader {String} X-Api-Key Contact uss unique access-key.
	 * @apiHeader {String} X-Token Contact uss unique token.
	 * @apiPermission Contact us Cant be Accessed permission name : api_contact_us_all
	 *
	 * @apiParam {String} [Filter=null] Optional filter of Contact uss.
	 * @apiParam {String} [Field="All Field"] Optional field of Contact uss : id, name, email, title, message.
	 * @apiParam {String} [Start=0] Optional start index of Contact uss.
	 * @apiParam {String} [Limit=10] Optional limit data of Contact uss.
	 *
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of contact_us.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError NoDataContact us Contact us data is nothing.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function all_get()
	{
		$this->is_allowed('api_contact_us_all');

		$filter = $this->get('filter');
		$field = $this->get('field');
		$limit = $this->get('limit') ? $this->get('limit') : $this->limit_page;
		$start = $this->get('start');

		$select_field = ['id', 'name', 'email', 'title', 'message'];
		$contact_uss = $this->model_api_contact_us->get($filter, $field, $limit, $start, $select_field);
		$total = $this->model_api_contact_us->count_all($filter, $field);

		$data['contact_us'] = $contact_uss;
				
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Contact us',
			'data'	 	=> $data,
			'total' 	=> $total
		], API::HTTP_OK);
	}

	
	/**
	 * @api {get} /contact_us/detail Detail Contact us.
	 * @apiVersion 0.1.0
	 * @apiName DetailContact us
	 * @apiGroup contact_us
	 * @apiHeader {String} X-Api-Key Contact uss unique access-key.
	 * @apiHeader {String} X-Token Contact uss unique token.
	 * @apiPermission Contact us Cant be Accessed permission name : api_contact_us_detail
	 *
	 * @apiParam {Integer} Id Mandatory id of Contact uss.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of contact_us.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError Contact usNotFound Contact us data is not found.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function detail_get()
	{
		$this->is_allowed('api_contact_us_detail');

		$this->requiredInput(['id']);

		$id = $this->get('id');

		$select_field = ['id', 'name', 'email', 'title', 'message'];
		$data['contact_us'] = $this->model_api_contact_us->find($id, $select_field);

		if ($data['contact_us']) {
			
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Contact us',
				'data'	 	=> $data
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Contact us not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

	
	/**
	 * @api {post} /contact_us/add Add Contact us.
	 * @apiVersion 0.1.0
	 * @apiName AddContact us
	 * @apiGroup contact_us
	 * @apiHeader {String} X-Api-Key Contact uss unique access-key.
	 * @apiHeader {String} X-Token Contact uss unique token.
	 * @apiPermission Contact us Cant be Accessed permission name : api_contact_us_add
	 *
 	 * @apiParam {String} Name Mandatory name of Contact uss. Input Name Max Length : 100. 
	 * @apiParam {String} Email Mandatory email of Contact uss. Input Email Max Length : 100. 
	 * @apiParam {String} Title Mandatory title of Contact uss. Input Title Max Length : 100. 
	 * @apiParam {String} Message Mandatory message of Contact uss.  
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
		$this->is_allowed('api_contact_us_add');

		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('message', 'Message', 'trim|required');
		
		if ($this->form_validation->run()) {


			$this->email->from($this->input->post('email'),$this->input->post('name'));
            $this->email->to('amr.zaky367@yahoo.com');
            $this->email->subject($this->input->post('title'));
            $this->email->message($this->input->post('message'));
            $this->email->send();


			$save_data = [
				'name' => $this->input->post('name'),
				'email' => $this->input->post('email'),
				'title' => $this->input->post('title'),
				'message' => $this->input->post('message'),
			];
			
			$save_contact_us = $this->model_api_contact_us->store($save_data);

			if ($save_contact_us) {
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
	 * @api {post} /contact_us/update Update Contact us.
	 * @apiVersion 0.1.0
	 * @apiName UpdateContact us
	 * @apiGroup contact_us
	 * @apiHeader {String} X-Api-Key Contact uss unique access-key.
	 * @apiHeader {String} X-Token Contact uss unique token.
	 * @apiPermission Contact us Cant be Accessed permission name : api_contact_us_update
	 *
	 * @apiParam {Integer} id Mandatory id of Contact Us.
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
		$this->is_allowed('api_contact_us_update');

		
		
		if ($this->form_validation->run()) {

			$save_data = [
			];
			
			$save_contact_us = $this->model_api_contact_us->change($this->post('id'), $save_data);

			if ($save_contact_us) {
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
	 * @api {post} /contact_us/delete Delete Contact us. 
	 * @apiVersion 0.1.0
	 * @apiName DeleteContact us
	 * @apiGroup contact_us
	 * @apiHeader {String} X-Api-Key Contact uss unique access-key.
	 * @apiHeader {String} X-Token Contact uss unique token.
	 	 * @apiPermission Contact us Cant be Accessed permission name : api_contact_us_delete
	 *
	 * @apiParam {Integer} Id Mandatory id of Contact uss .
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
		$this->is_allowed('api_contact_us_delete');

		$contact_us = $this->model_api_contact_us->find($this->post('id'));

		if (!$contact_us) {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Contact us not found'
			], API::HTTP_NOT_ACCEPTABLE);
		} else {
			$delete = $this->model_api_contact_us->remove($this->post('id'));

			}
		
		if ($delete) {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Contact us deleted',
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Contact us not delete'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

}

/* End of file Contact us.php */
/* Location: ./application/controllers/api/Contact us.php */