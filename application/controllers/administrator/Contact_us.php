<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Contact Us Controller
*| --------------------------------------------------------------------------
*| Contact Us site
*|
*/
class Contact_us extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_contact_us');
	}

	/**
	* show all Contact Uss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('contact_us_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['contact_uss'] = $this->model_contact_us->get($filter, $field, $this->limit_page, $offset);
		$this->data['contact_us_counts'] = $this->model_contact_us->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/contact_us/index/',
			'total_rows'   => $this->model_contact_us->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Contact Us List');
		$this->render('backend/standart/administrator/contact_us/contact_us_list', $this->data);
	}
	
	/**
	* Add new contact_uss
	*
	*/
	public function add()
	{
		$this->is_allowed('contact_us_add');

		$this->template->title('Contact Us New');
		$this->render('backend/standart/administrator/contact_us/contact_us_add', $this->data);
	}

	/**
	* Add New Contact Uss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('contact_us_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		

		if ($this->form_validation->run()) {
		
			$save_data = [
			];

			
			$save_contact_us = $this->model_contact_us->store($save_data);

			if ($save_contact_us) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_contact_us;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/contact_us/edit/' . $save_contact_us, 'Edit Contact Us'),
						anchor('administrator/contact_us', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/contact_us/edit/' . $save_contact_us, 'Edit Contact Us')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/contact_us');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/contact_us');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Contact Uss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('contact_us_update');

		$this->data['contact_us'] = $this->model_contact_us->find($id);

		$this->template->title('Contact Us Update');
		$this->render('backend/standart/administrator/contact_us/contact_us_update', $this->data);
	}

	/**
	* Update Contact Uss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('contact_us_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		
		if ($this->form_validation->run()) {
		
			$save_data = [
			];

			
			$save_contact_us = $this->model_contact_us->change($id, $save_data);

			if ($save_contact_us) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/contact_us', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/contact_us');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/contact_us');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Contact Uss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('contact_us_delete');

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
            set_message(cclang('has_been_deleted', 'contact_us'), 'success');
        } else {
            set_message(cclang('error_delete', 'contact_us'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Contact Uss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('contact_us_view');

		$this->data['contact_us'] = $this->model_contact_us->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Contact Us Detail');
		$this->render('backend/standart/administrator/contact_us/contact_us_view', $this->data);
	}
	
	/**
	* delete Contact Uss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$contact_us = $this->model_contact_us->find($id);

		
		
		return $this->model_contact_us->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('contact_us_export');

		$this->model_contact_us->export('contact_us', 'contact_us');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('contact_us_export');

		$this->model_contact_us->pdf('contact_us', 'contact_us');
	}
}


/* End of file contact_us.php */
/* Location: ./application/controllers/administrator/Contact Us.php */