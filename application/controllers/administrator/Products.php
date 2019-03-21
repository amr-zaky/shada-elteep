<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Products Controller
*| --------------------------------------------------------------------------
*| Products site
*|
*/
class Products extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_products');
	}

	/**
	* show all Productss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('products_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['productss'] = $this->model_products->get($filter, $field, $this->limit_page, $offset);
		$this->data['products_counts'] = $this->model_products->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/products/index/',
			'total_rows'   => $this->model_products->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Products List');
		$this->render('backend/standart/administrator/products/products_list', $this->data);
	}
	
	/**
	* Add new productss
	*
	*/
	public function add()
	{
		$this->is_allowed('products_add');

		$this->template->title('Products New');
		$this->render('backend/standart/administrator/products/products_add', $this->data);
	}

	/**
	* Add New Productss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('products_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('product_name', 'Product Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('product_count', 'Product Count', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('product_price', 'Product Price', 'trim|required');
		$this->form_validation->set_rules('products_product_image_name', 'Product Image', 'trim|required');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		$this->form_validation->set_rules('cat_id', 'Cat Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('barcode', 'Barcode', 'trim|max_length[11]');
		

		if ($this->form_validation->run()) {
			$products_product_image_uuid = $this->input->post('products_product_image_uuid');
			$products_product_image_name = $this->input->post('products_product_image_name');
		
			$save_data = [
				'product_name' => $this->input->post('product_name'),
				'product_count' => $this->input->post('product_count'),
				'product_price' => $this->input->post('product_price'),
				'status' => $this->input->post('status'),
				'cat_id' => $this->input->post('cat_id'),
				'description' => $this->input->post('description'),
				'barcode' => $this->input->post('barcode'),
				'product_price_offer' => $this->input->post('product_price_offer'),
			];

			if (!is_dir(FCPATH . '/uploads/products/')) {
				mkdir(FCPATH . '/uploads/products/');
			}

			if (!empty($products_product_image_name)) {
				$products_product_image_name_copy = date('YmdHis') . '-' . $products_product_image_name;

				rename(FCPATH . 'uploads/tmp/' . $products_product_image_uuid . '/' . $products_product_image_name, 
						FCPATH . 'uploads/products/' . $products_product_image_name_copy);

				if (!is_file(FCPATH . '/uploads/products/' . $products_product_image_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['product_image'] = $products_product_image_name_copy;
			}
		
			
			$save_products = $this->model_products->store($save_data);

			if ($save_products) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_products;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/products/edit/' . $save_products, 'Edit Products'),
						anchor('administrator/products', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/products/edit/' . $save_products, 'Edit Products')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/products');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/products');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Productss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('products_update');

		$this->data['products'] = $this->model_products->find($id);

		$this->template->title('Products Update');
		$this->render('backend/standart/administrator/products/products_update', $this->data);
	}

	/**
	* Update Productss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('products_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('product_name', 'Product Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('product_count', 'Product Count', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('product_price', 'Product Price', 'trim|required');
		$this->form_validation->set_rules('products_product_image_name', 'Product Image', 'trim|required');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		$this->form_validation->set_rules('cat_id', 'Cat Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('barcode', 'Barcode', 'trim|max_length[11]');
		
		if ($this->form_validation->run()) {
			$products_product_image_uuid = $this->input->post('products_product_image_uuid');
			$products_product_image_name = $this->input->post('products_product_image_name');
		
			$save_data = [
				'product_name' => $this->input->post('product_name'),
				'product_count' => $this->input->post('product_count'),
				'product_price' => $this->input->post('product_price'),
				'status' => $this->input->post('status'),
				'cat_id' => $this->input->post('cat_id'),
				'description' => $this->input->post('description'),
				'barcode' => $this->input->post('barcode'),
				'product_price_offer' => $this->input->post('product_price_offer'),
			];

			if (!is_dir(FCPATH . '/uploads/products/')) {
				mkdir(FCPATH . '/uploads/products/');
			}

			if (!empty($products_product_image_uuid)) {
				$products_product_image_name_copy = date('YmdHis') . '-' . $products_product_image_name;

				rename(FCPATH . 'uploads/tmp/' . $products_product_image_uuid . '/' . $products_product_image_name, 
						FCPATH . 'uploads/products/' . $products_product_image_name_copy);

				if (!is_file(FCPATH . '/uploads/products/' . $products_product_image_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['product_image'] = $products_product_image_name_copy;
			}
		
			
			$save_products = $this->model_products->change($id, $save_data);

			if ($save_products) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/products', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/products');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/products');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Productss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('products_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'products'), 'success');
        } else {
            set_message(cclang('error_delete', 'products'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Productss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('products_view');

		$this->data['products'] = $this->model_products->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Products Detail');
		$this->render('backend/standart/administrator/products/products_view', $this->data);
	}
	
	/**
	* delete Productss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$products = $this->model_products->find($id);

		if (!empty($products->product_image)) {
			$path = FCPATH . '/uploads/products/' . $products->product_image;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_products->remove($id);
	}
	
	/**
	* Upload Image Products	* 
	* @return JSON
	*/
	public function upload_product_image_file()
	{
		if (!$this->is_allowed('products_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'products',
		]);
	}

	/**
	* Delete Image Products	* 
	* @return JSON
	*/
	public function delete_product_image_file($uuid)
	{
		if (!$this->is_allowed('products_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'product_image', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'products',
            'primary_key'       => 'product_id',
            'upload_path'       => 'uploads/products/'
        ]);
	}

	/**
	* Get Image Products	* 
	* @return JSON
	*/
	public function get_product_image_file($id)
	{
		if (!$this->is_allowed('products_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$products = $this->model_products->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'product_image', 
            'table_name'        => 'products',
            'primary_key'       => 'product_id',
            'upload_path'       => 'uploads/products/',
            'delete_endpoint'   => 'administrator/products/delete_product_image_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('products_export');

		$this->model_products->export('products', 'products');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('products_export');

		$this->model_products->pdf('products', 'products');
	}
}


/* End of file products.php */
/* Location: ./application/controllers/administrator/Products.php */