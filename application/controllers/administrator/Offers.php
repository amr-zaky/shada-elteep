<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Offers Controller
*| --------------------------------------------------------------------------
*| Offers site
*|
*/
class Offers extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_offers');
	}

	/**
	* show all Offerss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('offers_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['offerss'] = $this->model_offers->get($filter, $field, $this->limit_page, $offset);
		$this->data['offers_counts'] = $this->model_offers->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/offers/index/',
			'total_rows'   => $this->model_offers->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Offers List');
		$this->render('backend/standart/administrator/offers/offers_list', $this->data);
	}
	
	/**
	* Add new offerss
	*
	*/
	public function add()
	{
		$this->is_allowed('offers_add');

		$this->template->title('Offers New');
		$this->render('backend/standart/administrator/offers/offers_add', $this->data);
	}

	/**
	* Add New Offerss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('offers_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('pro_id', 'Pro Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('end_date', 'End Date', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'pro_id' => $this->input->post('pro_id'),
				'end_date' => $this->input->post('end_date'),
			];

			
			$save_offers = $this->model_offers->store($save_data);

			if ($save_offers) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_offers;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/offers/edit/' . $save_offers, 'Edit Offers'),
						anchor('administrator/offers', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/offers/edit/' . $save_offers, 'Edit Offers')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/offers');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/offers');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Offerss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('offers_update');

		$this->data['offers'] = $this->model_offers->find($id);

		$this->template->title('Offers Update');
		$this->render('backend/standart/administrator/offers/offers_update', $this->data);
	}

	/**
	* Update Offerss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('offers_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('pro_id', 'Pro Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('end_date', 'End Date', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'pro_id' => $this->input->post('pro_id'),
				'end_date' => $this->input->post('end_date'),
			];

			
			$save_offers = $this->model_offers->change($id, $save_data);

			if ($save_offers) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/offers', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/offers');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/offers');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Offerss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('offers_delete');

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
            set_message(cclang('has_been_deleted', 'offers'), 'success');
        } else {
            set_message(cclang('error_delete', 'offers'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Offerss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('offers_view');

		$this->data['offers'] = $this->model_offers->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Offers Detail');
		$this->render('backend/standart/administrator/offers/offers_view', $this->data);
	}
	
	/**
	* delete Offerss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$offers = $this->model_offers->find($id);

		
		
		return $this->model_offers->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('offers_export');

		$this->model_offers->export('offers', 'offers');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('offers_export');

		$this->model_offers->pdf('offers', 'offers');
	}
}


/* End of file offers.php */
/* Location: ./application/controllers/administrator/Offers.php */