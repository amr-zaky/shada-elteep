<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Areas Controller
*| --------------------------------------------------------------------------
*| Areas site
*|
*/
class Areas extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_areas');
	}

	/**
	* show all Areass
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('areas_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['areass'] = $this->model_areas->get($filter, $field, $this->limit_page, $offset);
		$this->data['areas_counts'] = $this->model_areas->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/areas/index/',
			'total_rows'   => $this->model_areas->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Areas List');
		$this->render('backend/standart/administrator/areas/areas_list', $this->data);
	}
	
	/**
	* Add new areass
	*
	*/
	public function add()
	{
		$this->is_allowed('areas_add');

		$this->template->title('Areas New');
		$this->render('backend/standart/administrator/areas/areas_add', $this->data);
	}

	/**
	* Add New Areass
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('areas_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('areas_name', 'Areas Name', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'areas_name' => $this->input->post('areas_name'),
			];

			
			$save_areas = $this->model_areas->store($save_data);

			if ($save_areas) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_areas;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/areas/edit/' . $save_areas, 'Edit Areas'),
						anchor('administrator/areas', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/areas/edit/' . $save_areas, 'Edit Areas')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/areas');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/areas');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Areass
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('areas_update');

		$this->data['areas'] = $this->model_areas->find($id);

		$this->template->title('Areas Update');
		$this->render('backend/standart/administrator/areas/areas_update', $this->data);
	}

	/**
	* Update Areass
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('areas_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('areas_name', 'Areas Name', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'areas_name' => $this->input->post('areas_name'),
			];

			
			$save_areas = $this->model_areas->change($id, $save_data);

			if ($save_areas) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/areas', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/areas');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/areas');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Areass
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('areas_delete');

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
            set_message(cclang('has_been_deleted', 'areas'), 'success');
        } else {
            set_message(cclang('error_delete', 'areas'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Areass
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('areas_view');

		$this->data['areas'] = $this->model_areas->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Areas Detail');
		$this->render('backend/standart/administrator/areas/areas_view', $this->data);
	}
	
	/**
	* delete Areass
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$areas = $this->model_areas->find($id);

		
		
		return $this->model_areas->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('areas_export');

		$this->model_areas->export('areas', 'areas');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('areas_export');

		$this->model_areas->pdf('areas', 'areas');
	}
}


/* End of file areas.php */
/* Location: ./application/controllers/administrator/Areas.php */