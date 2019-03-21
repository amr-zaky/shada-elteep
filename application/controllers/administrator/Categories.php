<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Categories Controller
*| --------------------------------------------------------------------------
*| Categories site
*|
*/
class Categories extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_categories');
	}

	/**
	* show all Categoriess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('categories_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['categoriess'] = $this->model_categories->get($filter, $field, $this->limit_page, $offset);
		$this->data['categories_counts'] = $this->model_categories->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/categories/index/',
			'total_rows'   => $this->model_categories->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Categories List');
		$this->render('backend/standart/administrator/categories/categories_list', $this->data);
	}
	
	/**
	* Add new categoriess
	*
	*/
	public function add()
	{
		$this->is_allowed('categories_add');

		$this->template->title('Categories New');
		$this->render('backend/standart/administrator/categories/categories_add', $this->data);
	}

	/**
	* Add New Categoriess
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('categories_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('category_name', 'Category Name', 'trim|required|max_length[150]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'category_name' => $this->input->post('category_name'),
			];

			
			$save_categories = $this->model_categories->store($save_data);

			if ($save_categories) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_categories;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/categories/edit/' . $save_categories, 'Edit Categories'),
						anchor('administrator/categories', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/categories/edit/' . $save_categories, 'Edit Categories')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/categories');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/categories');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Categoriess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('categories_update');

		$this->data['categories'] = $this->model_categories->find($id);

		$this->template->title('Categories Update');
		$this->render('backend/standart/administrator/categories/categories_update', $this->data);
	}

	/**
	* Update Categoriess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('categories_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('category_name', 'Category Name', 'trim|required|max_length[150]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'category_name' => $this->input->post('category_name'),
			];

			
			$save_categories = $this->model_categories->change($id, $save_data);

			if ($save_categories) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/categories', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/categories');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/categories');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Categoriess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('categories_delete');

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
            set_message(cclang('has_been_deleted', 'categories'), 'success');
        } else {
            set_message(cclang('error_delete', 'categories'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Categoriess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('categories_view');

		$this->data['categories'] = $this->model_categories->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Categories Detail');
		$this->render('backend/standart/administrator/categories/categories_view', $this->data);
	}
	
	/**
	* delete Categoriess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$categories = $this->model_categories->find($id);

		
		
		return $this->model_categories->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('categories_export');

		$this->model_categories->export('categories', 'categories');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('categories_export');

		$this->model_categories->pdf('categories', 'categories');
	}
}


/* End of file categories.php */
/* Location: ./application/controllers/administrator/Categories.php */