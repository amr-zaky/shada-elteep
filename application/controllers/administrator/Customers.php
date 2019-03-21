<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Customers Controller
*| --------------------------------------------------------------------------
*| Customers site
*|
*/
class Customers extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_customers');
	}

	/**
	* show all Customerss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('customers_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['customerss'] = $this->model_customers->get($filter, $field, $this->limit_page, $offset);
		$this->data['customers_counts'] = $this->model_customers->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/customers/index/',
			'total_rows'   => $this->model_customers->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Customers List');
		$this->render('backend/standart/administrator/customers/customers_list', $this->data);
	}
	
	/**
	* Add new customerss
	*
	*/
	public function add()
	{
		$this->is_allowed('customers_add');

		$this->template->title('Customers New');
		$this->render('backend/standart/administrator/customers/customers_add', $this->data);
	}

	/**
	* Add New Customerss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('customers_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		

		if ($this->form_validation->run()) {
		
			$save_data = [
			];

			if (!is_dir(FCPATH . '/uploads/customers/')) {
				mkdir(FCPATH . '/uploads/customers/');
			}

			
			$save_customers = $this->model_customers->store($save_data);

			if ($save_customers) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_customers;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/customers/edit/' . $save_customers, 'Edit Customers'),
						anchor('administrator/customers', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/customers/edit/' . $save_customers, 'Edit Customers')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/customers');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/customers');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Customerss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('customers_update');

		$this->data['customers'] = $this->model_customers->find($id);

		$this->template->title('Customers Update');
		$this->render('backend/standart/administrator/customers/customers_update', $this->data);
	}

	/**
	* Update Customerss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('customers_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'is_active' => implode(',', (array) $this->input->post('is_active')),
			];

			if (!is_dir(FCPATH . '/uploads/customers/')) {
				mkdir(FCPATH . '/uploads/customers/');
			}

			
			$save_customers = $this->model_customers->change($id, $save_data);

			if ($save_customers) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/customers', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/customers');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/customers');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Customerss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('customers_delete');

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
            set_message(cclang('has_been_deleted', 'customers'), 'success');
        } else {
            set_message(cclang('error_delete', 'customers'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Customerss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('customers_view');

		$this->data['customers'] = $this->model_customers->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Customers Detail');
		$this->render('backend/standart/administrator/customers/customers_view', $this->data);
	}
	
	/**
	* delete Customerss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$customers = $this->model_customers->find($id);

		if (!empty($customers->image)) {
			$path = FCPATH . '/uploads/customers/' . $customers->image;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_customers->remove($id);
	}
	
	/**
	* Upload Image Customers	* 
	* @return JSON
	*/
	public function upload_image_file()
	{
		if (!$this->is_allowed('customers_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'customers',
		]);
	}

	/**
	* Delete Image Customers	* 
	* @return JSON
	*/
	public function delete_image_file($uuid)
	{
		if (!$this->is_allowed('customers_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'image', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'customers',
            'primary_key'       => 'customer_id',
            'upload_path'       => 'uploads/customers/'
        ]);
	}

	/**
	* Get Image Customers	* 
	* @return JSON
	*/
	public function get_image_file($id)
	{
		if (!$this->is_allowed('customers_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$customers = $this->model_customers->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'image', 
            'table_name'        => 'customers',
            'primary_key'       => 'customer_id',
            'upload_path'       => 'uploads/customers/',
            'delete_endpoint'   => 'administrator/customers/delete_image_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('customers_export');

		$this->model_customers->export('customers', 'customers');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('customers_export');

		$this->model_customers->pdf('customers', 'customers');
	}
}


/* End of file customers.php */
/* Location: ./application/controllers/administrator/Customers.php */