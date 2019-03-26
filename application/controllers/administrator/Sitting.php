<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Sitting Controller
*| --------------------------------------------------------------------------
*| Sitting site
*|
*/
class Sitting extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_sitting');
	}

	/**
	* show all Sittings
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('sitting_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['sittings'] = $this->model_sitting->get($filter, $field, $this->limit_page, $offset);
		$this->data['sitting_counts'] = $this->model_sitting->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/sitting/index/',
			'total_rows'   => $this->model_sitting->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Sitting List');
		$this->render('backend/standart/administrator/sitting/sitting_list', $this->data);
	}
	
	/**
	* Add new sittings
	*
	*/
	public function add()
	{
		$this->is_allowed('sitting_add');

		$this->template->title('Sitting New');
		$this->render('backend/standart/administrator/sitting/sitting_add', $this->data);
	}

	/**
	* Add New Sittings
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('sitting_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		

		if ($this->form_validation->run()) {
		
			$save_data = [
			];

			
			$save_sitting = $this->model_sitting->store($save_data);

			if ($save_sitting) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_sitting;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/sitting/edit/' . $save_sitting, 'Edit Sitting'),
						anchor('administrator/sitting', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/sitting/edit/' . $save_sitting, 'Edit Sitting')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/sitting');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/sitting');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Sittings
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('sitting_update');

		$this->data['sitting'] = $this->model_sitting->find($id);

		$this->template->title('Sitting Update');
		$this->render('backend/standart/administrator/sitting/sitting_update', $this->data);
	}

	/**
	* Update Sittings
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('sitting_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('page_name', 'Page Name', 'trim|required');
		$this->form_validation->set_rules('content', 'Content', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'page_name' => $this->input->post('page_name'),
				'content' => $this->input->post('content'),
			];

			
			$save_sitting = $this->model_sitting->change($id, $save_data);

			if ($save_sitting) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/sitting', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/sitting');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/sitting');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Sittings
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('sitting_delete');

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
            set_message(cclang('has_been_deleted', 'sitting'), 'success');
        } else {
            set_message(cclang('error_delete', 'sitting'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Sittings
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('sitting_view');

		$this->data['sitting'] = $this->model_sitting->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Sitting Detail');
		$this->render('backend/standart/administrator/sitting/sitting_view', $this->data);
	}
	
	/**
	* delete Sittings
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$sitting = $this->model_sitting->find($id);

		
		
		return $this->model_sitting->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('sitting_export');

		$this->model_sitting->export('sitting', 'sitting');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('sitting_export');

		$this->model_sitting->pdf('sitting', 'sitting');
	}
}


/* End of file sitting.php */
/* Location: ./application/controllers/administrator/Sitting.php */