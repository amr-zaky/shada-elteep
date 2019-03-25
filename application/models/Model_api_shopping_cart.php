<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_api_shopping_cart extends MY_Model {

	private $primary_key 	= 'cart_id';
	private $table_name 	= 'shopping_cart';
	private $field_search 	= ['cart_id', 'cust_id', 'pro_id', 'cart_count'];

	public function __construct()
	{
		$config = array(
			'primary_key' 	=> $this->primary_key,
		 	'table_name' 	=> $this->table_name,
		 	'field_search' 	=> $this->field_search,
		 );

		parent::__construct($config);
	}

	public function count_all($q = null, $field = null)
	{
		$iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
		$field = $this->scurity($field);

        if (empty($field)) {
	        foreach ($this->field_search as $field) {
	            if ($iterasi == 1) {
	                $where .= $field . " LIKE '%" . $q . "%' ";
	            } else {
	                $where .= "OR " . $field . " LIKE '%" . $q . "%' ";
	            }
	            $iterasi++;
	        }

	        $where = '('.$where.')';
        } else {
        	$where .= "(" . $field . " LIKE '%" . $q . "%' )";
        }

        $this->db->where($where);
		$query = $this->db->get($this->table_name);

		return $query->num_rows();
	}

	public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
	{
		$iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
		$field = $this->scurity($field);

        if (empty($field)) {
	        foreach ($this->field_search as $field) {
	            if ($iterasi == 1) {
	                $where .= $field . " LIKE '%" . $q . "%' ";
	            } else {
	                $where .= "OR " . $field . " LIKE '%" . $q . "%' ";
	            }
	            $iterasi++;
	        }

	        $where = '('.$where.')';
        } else {
        	if (in_array($field, $select_field)) {
        		$where .= "(" . $field . " LIKE '%" . $q . "%' )";
        	}
        }

        if (is_array($select_field) AND count($select_field)) {
        	$this->db->select($select_field);
        }
		
		if ($where) {
        	$this->db->where($where);
		}
        $this->db->limit($limit, $offset);
        $this->db->order_by($this->primary_key, "DESC");
		$query = $this->db->get($this->table_name);

		return $query->result();
	}


	public function getallcardd($cust_id)
	{
		$this->db->select('products.*,favorites.pro_id as is_favorites');
		$this->db->from('shopping_cart');
		$this->db->where('shopping_cart.cust_id',$cust_id);
		$this->db->join('favorites','favorites.pro_id=shopping_cart.pro_id','left');


		$this->db->join('products','products.product_id=shopping_cart.pro_id');
		$query=$this->db->get();
		$data=$query->result();
		return $data;
	}


	
	public function getProductPrice($cust_id)
	{	
		$this->db->select('products.*,offers.pro_id,shopping_cart.cart_count as amount');
		$this->db->from('shopping_cart');

		$this->db->join('offers','offers.pro_id =shopping_cart.pro_id','left');
		$this->db->join('products','products.product_id =shopping_cart.pro_id');
		$this->db->where('shopping_cart.cust_id',$cust_id);
		

		$query=$this->db->get();
		$data=$query->result();
		return $data;
	}



	public function getShippingDetail()
	{
		$this->db->select('*');
		$this->db->from('shipping_details');
		$query=$this->db->get();
		$data=$query->result();
		return $data;
	}
	

}

/* End of file Model_shopping_cart.php */
/* Location: ./application/models/Model_shopping_cart.php */