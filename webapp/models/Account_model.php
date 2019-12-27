<?php
class Account_model extends CI_Model {
	private $db_main  = null;
    private $db_query = null;
    public function __construct() {
        parent::__construct();
        $this->db_main = $this->load->database('db_main',TRUE, true);
        $this->db_query = $this->load->database('db_query', TRUE, true);
    }
    public function get_account_by_name($account_name){
    	if(empty($account_name))return false;
    	
    	if (!$query = $this->db_query->get_where('t_account', ['account_name'=>$account_name])) {
    		$e = $this->db_query->error();
    		throw new Exception($e['message'], $e['code']);
    	}
    	return $query->row_array();
    }

    public function get_account_by_id($account_id){
        if(empty($account_id))return false;
        $this->db_query->select('*');
        $this->db_query->from('t_account');
        $this->db_query->where('pk_account',$account_id);
        if(!$query = $this->db_query->get()){
            $e = $this->db_query->error();
            throw new Exception($e['message'], $e['code']);
        }
        $account = $query->row_array();
        if(empty($account))return false;
        return $account;
    }
    public function get_account_by_city($city){
        if(empty($city))return false;
        $this->db_query->select('*');
        $this->db_query->from('t_account');
        $this->db_query->where('city',$city);
        if(!$query = $this->db_query->get()){
            $e = $this->db_query->error();
            throw new Exception($e['message'], $e['code']);
        }
        $r = $query->result_array();
        if(empty($r))return false;
        return $r;
    }


    
    public function update_pwd($account_id,$new_pwd){
        $fields['account_pwd'] = $new_pwd;
        
        if(!$this->db_main->set($fields)->
                where('pk_account', $account_id)->
                update('t_account')) {
            $error = $this->db_main->error();
            throw new Exception($error['message'], $error['code']);
        }
        $affected_rows = $this->db_main->affected_rows();
        return $affected_rows;
        
    }
    
    
}