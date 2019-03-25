<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Shopping_cart extends API
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_api_shopping_cart');
	}

	/**
	 * @api {get} /shopping_cart/all Get all shopping_carts.
	 * @apiVersion 0.1.0
	 * @apiName AllShoppingcart 
	 * @apiGroup shopping_cart
	 * @apiHeader {String} X-Api-Key Shopping carts unique access-key.
	 * @apiHeader {String} X-Token Shopping carts unique token.
	 * @apiPermission Shopping cart Cant be Accessed permission name : api_shopping_cart_all
	 *
	 * @apiParam {String} [Filter=null] Optional filter of Shopping carts.
	 * @apiParam {String} [Field="All Field"] Optional field of Shopping carts : cart_id, cust_id, pro_id, cart_count.
	 * @apiParam {String} [Start=0] Optional start index of Shopping carts.
	 * @apiParam {String} [Limit=10] Optional limit data of Shopping carts.
	 *
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of shopping_cart.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError NoDataShopping cart Shopping cart data is nothing.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function all_get()
	{
		$this->is_allowed('api_shopping_cart_all');

		$filter = $this->get('filter');
		$field = $this->get('field');
		$limit = $this->get('limit') ? $this->get('limit') : $this->limit_page;
		$start = $this->get('start');

		$select_field = ['cart_id', 'cust_id', 'pro_id', 'cart_count'];
		$shopping_carts = $this->model_api_shopping_cart->get($filter, $field, $limit, $start, $select_field);
		$total = $this->model_api_shopping_cart->count_all($filter, $field);

		$data['shopping_cart'] = $shopping_carts;
				
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Shopping cart',
			'data'	 	=> $data,
			'total' 	=> $total
		], API::HTTP_OK);
	}



	public function getallcard_post()
	{
		$this->is_allowed('api_shopping_cart_all');
		$this->form_validation->set_rules('cust_id', 'Cust Id', 'trim|required|max_length[11]');

     $cust_id=$this->input->post('cust_id');

     	$data['shopping_cart'] = $this->model_api_shopping_cart->getallcardd($cust_id);
					
		$this->response([
			'status' 	=> true,
			'message' 	=> 'Data Shopping cart',
			'data'	 	=> $data
			
		], API::HTTP_OK);

	}
	
	/**
	 * @api {get} /shopping_cart/detail Detail Shopping cart.
	 * @apiVersion 0.1.0
	 * @apiName DetailShopping cart
	 * @apiGroup shopping_cart
	 * @apiHeader {String} X-Api-Key Shopping carts unique access-key.
	 * @apiHeader {String} X-Token Shopping carts unique token.
	 * @apiPermission Shopping cart Cant be Accessed permission name : api_shopping_cart_detail
	 *
	 * @apiParam {Integer} Id Mandatory id of Shopping carts.
	 *
	 * @apiSuccess {Boolean} Status status response api.
	 * @apiSuccess {String} Message message response api.
	 * @apiSuccess {Array} Data data of shopping_cart.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *
	 * @apiError Shopping cartNotFound Shopping cart data is not found.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 403 Not Acceptable
	 *
	 */
	public function detail_get()
	{
		$this->is_allowed('api_shopping_cart_detail');

		$this->requiredInput(['cart_id']);

		$id = $this->get('cart_id');

		$select_field = ['cart_id', 'cust_id', 'pro_id', 'cart_count'];
		$data['shopping_cart'] = $this->model_api_shopping_cart->find($id, $select_field);

		if ($data['shopping_cart']) {
			
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Detail Shopping cart',
				'data'	 	=> $data
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Shopping cart not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

	public function calculateprice_get()
	{
		$this->is_allowed('api_shopping_cart_detail');

		$this->requiredInput(['cust_id']);

		$id =$this->get('cust_id');

		$data['shopping_cart'] = $this->model_api_shopping_cart->getProductPrice($id);

		$totalPrice=0;
		foreach ($data['shopping_cart'] as $product)
		{
			
			if(@$product->pro_id)
			{
				$totalPrice=$totalPrice+($product->product_price_offer * $product->amount);
			}
			else 
			{
				$totalPrice=$totalPrice+($product->product_price * $product->amount);
			}
			
		}

		$data['shipping_details'] = $this->model_api_shopping_cart->getShippingDetail();

		$Shipping_price=$data['shipping_details'][0]->shipping_price;

		$Shipping_taxes=$data['shipping_details'][0]->taxes;

			

			$totalPrice=$totalPrice+$Shipping_price;
			$texesPresentage=$totalPrice*($Shipping_taxes/100);
			$totalPrice=$totalPrice +$texesPresentage;
			

			
			$price['price_detail'][]=[
				'total_price'=>$totalPrice,
				'Shipping_price'=>$Shipping_price,
				'Shipping_taxes'=>$Shipping_taxes
			];
		if ($data['shopping_cart']) {
			
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Total Price',	
				'data'=> $price
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Shopping cart not found'
			], API::HTTP_NOT_ACCEPTABLE);
		}

	}	
	/**
	 * @api {post} /shopping_cart/add Add Shopping cart.
	 * @apiVersion 0.1.0
	 * @apiName AddShopping cart
	 * @apiGroup shopping_cart
	 * @apiHeader {String} X-Api-Key Shopping carts unique access-key.
	 * @apiHeader {String} X-Token Shopping carts unique token.
	 * @apiPermission Shopping cart Cant be Accessed permission name : api_shopping_cart_add
	 *
 	 * @apiParam {String} Cust_id Mandatory cust_id of Shopping carts. Input Cust Id Max Length : 11. 
	 * @apiParam {String} Pro_id Mandatory pro_id of Shopping carts. Input Pro Id Max Length : 11. 
	 * @apiParam {String} Cart_count Mandatory cart_count of Shopping carts. Input Cart Count Max Length : 11. 
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
		$this->is_allowed('api_shopping_cart_add');

		$this->form_validation->set_rules('cust_id', 'Cust Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('pro_id', 'Pro Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('cart_count', 'Cart Count', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {

			$save_data = [
				'cust_id' => $this->input->post('cust_id'),
				'pro_id' => $this->input->post('pro_id'),
				'cart_count' => $this->input->post('cart_count'),
			];
			
			$save_shopping_cart = $this->model_api_shopping_cart->store($save_data);

			if ($save_shopping_cart) {
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
	 * @api {post} /shopping_cart/update Update Shopping cart.
	 * @apiVersion 0.1.0
	 * @apiName UpdateShopping cart
	 * @apiGroup shopping_cart
	 * @apiHeader {String} X-Api-Key Shopping carts unique access-key.
	 * @apiHeader {String} X-Token Shopping carts unique token.
	 * @apiPermission Shopping cart Cant be Accessed permission name : api_shopping_cart_update
	 *
	 * @apiParam {String} Cust_id Mandatory cust_id of Shopping carts. Input Cust Id Max Length : 11. 
	 * @apiParam {String} Pro_id Mandatory pro_id of Shopping carts. Input Pro Id Max Length : 11. 
	 * @apiParam {String} Cart_count Mandatory cart_count of Shopping carts. Input Cart Count Max Length : 11. 
	 * @apiParam {Integer} cart_id Mandatory cart_id of Shopping Cart.
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
		$this->is_allowed('api_shopping_cart_update');

		
		
		$this->form_validation->set_rules('cart_count', 'Cart Count', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {

			$save_data = [
				
				'cart_count' => $this->input->post('cart_count'),
			];
			
			$save_shopping_cart = $this->model_api_shopping_cart->change($this->post('cart_id'), $save_data);

			if ($save_shopping_cart) {
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
	 * @api {post} /shopping_cart/delete Delete Shopping cart. 
	 * @apiVersion 0.1.0
	 * @apiName DeleteShopping cart
	 * @apiGroup shopping_cart
	 * @apiHeader {String} X-Api-Key Shopping carts unique access-key.
	 * @apiHeader {String} X-Token Shopping carts unique token.
	 	 * @apiPermission Shopping cart Cant be Accessed permission name : api_shopping_cart_delete
	 *
	 * @apiParam {Integer} Id Mandatory id of Shopping carts .
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
		$this->is_allowed('api_shopping_cart_delete');

		$shopping_cart = $this->model_api_shopping_cart->find($this->post('cart_id'));

		if (!$shopping_cart) {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Shopping cart not found'
			], API::HTTP_NOT_ACCEPTABLE);
		} else {
			$delete = $this->model_api_shopping_cart->remove($this->post('cart_id'));

			}
		
		if ($delete) {
			$this->response([
				'status' 	=> true,
				'message' 	=> 'Shopping cart deleted',
			], API::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> false,
				'message' 	=> 'Shopping cart not delete'
			], API::HTTP_NOT_ACCEPTABLE);
		}
	}

}

/* End of file Shopping cart.php */
/* Location: ./application/controllers/api/Shopping cart.php */