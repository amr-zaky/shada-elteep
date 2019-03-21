<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Coupons Controller
*| --------------------------------------------------------------------------
*| Coupons site
*|
*/
class Coupons extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_coupons');
	}

	/**
	* show all Couponss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('coupons_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['couponss'] = $this->model_coupons->get($filter, $field, $this->limit_page, $offset);
		$this->data['coupons_counts'] = $this->model_coupons->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/coupons/index/',
			'total_rows'   => $this->model_coupons->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Coupons List');
		$this->render('backend/standart/administrator/coupons/coupons_list', $this->data);
	}
	
	/**
	* Add new couponss
	*
	*/
	public function add()
	{
		$this->is_allowed('coupons_add');

		$this->template->title('Coupons New');
		$this->render('backend/standart/administrator/coupons/coupons_add', $this->data);
	}

	/**
	* Add New Couponss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('coupons_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('percentage', 'Percentage', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('code', 'Code', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'percentage' => $this->input->post('percentage'),
				'code' => $this->input->post('code'),
			];

			
			$save_coupons = $this->model_coupons->store($save_data);

			if ($save_coupons) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_coupons;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/coupons/edit/' . $save_coupons, 'Edit Coupons'),
						anchor('administrator/coupons', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/coupons/edit/' . $save_coupons, 'Edit Coupons')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/coupons');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/coupons');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Couponss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('coupons_update');

		$this->data['coupons'] = $this->model_coupons->find($id);

		$this->template->title('Coupons Update');
		$this->render('backend/standart/administrator/coupons/coupons_update', $this->data);
	}

	/**
	* Update Couponss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('coupons_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('percentage', 'Percentage', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('code', 'Code', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'percentage' => $this->input->post('percentage'),
				'code' => $this->input->post('code'),
			];

			
			$save_coupons = $this->model_coupons->change($id, $save_data);

			if ($save_coupons) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/coupons', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/coupons');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/coupons');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Couponss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('coupons_delete');

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
            set_message(cclang('has_been_deleted', 'coupons'), 'success');
        } else {
            set_message(cclang('error_delete', 'coupons'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Couponss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('coupons_view');

		$this->data['coupons'] = $this->model_coupons->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Coupons Detail');
		$this->render('backend/standart/administrator/coupons/coupons_view', $this->data);
	}
	
	/**
	* delete Couponss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$coupons = $this->model_coupons->find($id);

		
		
		return $this->model_coupons->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('coupons_export');

		$this->model_coupons->export('coupons', 'coupons');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('coupons_export');

		$this->model_coupons->pdf('coupons', 'coupons');
	}
}


/* End of file coupons.php */
/* Location: ./application/controllers/administrator/Coupons.php */