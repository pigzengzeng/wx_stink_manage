<?php
class Conf_model extends CI_Model {
	private $db_main  = null;
    private $db_query = null;
    public function __construct() {
        parent::__construct();
        $this->db_main = $this->load->database('db_main',TRUE, true);
        $this->db_query = $this->load->database('db_query', TRUE, true);
    }
    public function get_conf_message_by_account_id($account_id){
        if(empty($account_id))return false;
        $this->db_query->select('*');
        $this->db_query->from('t_conf_message');
        $this->db_query->where('fk_account',$account_id);
        if(!$query = $this->db_query->get()){
            $e = $this->db_query->error();
            throw new Exception($e['message'], $e['code']);
        }
        $row = $query->row_array();
        if(empty($row))return false;
        return $row;
    }

    public function get_conf_messages_by_account_id($account_id){
        if(empty($account_id))return false;
        $this->db_query->select('*');
        $this->db_query->from('t_conf_message');
        $this->db_query->where('fk_account',$account_id);
        if(!$query = $this->db_query->get()){
            $e = $this->db_query->error();
            throw new Exception($e['message'], $e['code']);
        }
        $r = $query->result_array();
        if(empty($r))return false;
        return $r;
    }


    public function get_conf_message($conf_message_id){
        if(empty($conf_message_id))return false;
        $this->db_query->select('*');
        $this->db_query->from('t_conf_message');
        $this->db_query->where('pk_conf_message',$conf_message_id);
        if(!$query = $this->db_query->get()){
            $e = $this->db_query->error();
            throw new Exception($e['message'], $e['code']);
        }
        $row = $query->row_array();
        if(empty($row))return false;
        return $row;
    }

    
    public function insert_conf_message($conf_message){
        if(empty($conf_message)) return 0;
        $data=$conf_message;        
        $this->db_main->insert('t_conf_message',$data);        
        $insert_id = $this->db_main->insert_id();
        return $insert_id;
    }

    public function update_conf_message($conf_message_id,$fields){
        if(empty($fields)) return 0;        
        if(!$this->db_main->set($fields)->
                where('pk_conf_message', $conf_message_id)->
                update('t_conf_message')) {
            $error = $this->db_main->error();
            throw new Exception($error['message'], $error['code']);
        }
        $affected_rows = $this->db_main->affected_rows();
        return $affected_rows;        
    }
    
}