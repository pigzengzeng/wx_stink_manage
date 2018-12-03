<?php
class User_model extends CI_Model {
	private $db_main  = null;
    private $db_query = null;
    public function __construct() {
        parent::__construct();
        $this->db_main = $this->load->database('db_main',TRUE, true);
        $this->db_query = $this->load->database('db_query', TRUE, true);
    }
    public function search($keyword='',$user_type,$state,$page=1,$size=10,$order_field='createtime',$order='desc'){
        $offset = ($page-1)*$size;
    	

        $query = $this->db_query->select('*');

        $where = [];
        if(!empty($keyword)){
            $where[] = "(nickname like '%".$this->db_query->escape_like_str($keyword)."%' ESCAPE '!'
            or realname like '%".$this->db_query->escape_like_str($keyword)."%' ESCAPE '!') ";
        }
        if(!empty($user_type)){
            foreach ($user_type as &$item) {
                $item = $this->db_query->escape( $item );
            }
            $where[] = "user_type in (". join(",",$user_type) .")";
        }
        if(!empty($state)){
            foreach ($state as &$item) {
                $item = $this->db_query->escape( $item );
            }

            $where[] = "state in (". join(",",$state) .")";
        }
        $query = $query->where(join(" and ",$where));





        $query = $query->limit($size,$offset);

        $query = $query->order_by($order_field,$order);
        $query = $query->get('t_user');

        //echo $this->db_query->last_query();
    	if (!$query) {
    		$e = $this->db_query->error();
    		throw new Exception($e['message'], $e['code']);
    	}
    	return $query->result_array();
    }

    public function get_total($keyword,$user_type=array(),$state=array()){
        $query = $this->db_query->select('count(*) as c');
        $where = [];
        if(!empty($keyword)){
            $where[] = "(nickname like '%".$this->db_query->escape_like_str($keyword)."%' ESCAPE '!'
            or realname like '%".$this->db_query->escape_like_str($keyword)."%' ESCAPE '!') ";
        }
        if(!empty($user_type)){
            foreach ($user_type as &$item) {
                $item = $this->db_query->escape( $item );
            }
            $where[] = "user_type in (". join(",",$user_type) .")";
        }
        if(!empty($state)){
            foreach ($state as &$item) {
                $item = $this->db_query->escape( $item );
            }

            $where[] = "state in (". join(",",$state) .")";
        }
        $query = $query->where(join(" and ",$where));
        
        $query = $query->get('t_user');
        //echo $this->db_query->last_query();
        if (!$query) {
            $e = $this->db_query->error();
            throw new Exception($e['message'], $e['code']);
            return false;
        }
        return $query->row_array()['c'];
    }

    public function get_user_by_userid($userid){
        if(empty($userid))return false;
        $this->db_query->select('*');
        $this->db_query->from('t_user');
        $this->db_query->where('pk_user',$userid);
        if(!$query = $this->db_query->get()){
            $e = $this->db_query->error();
            throw new Exception($e['message'], $e['code']);
        }
        $user = $query->row_array();
        if(empty($user))return false;
        return $user;
    }
    
    public function update_user($pk_user,$fields){
        if(empty($fields)) return 0;
        
        if(!$this->db_main->set($fields)->
                where('pk_user', $pk_user)->
                update('t_user')) {
            $error = $this->db_main->error();
            throw new Exception($error['message'], $error['code']);
        }
        $affected_rows = $this->db_main->affected_rows();
        return $affected_rows;
        
    }


    public function get_last_position(){
        $query = $this->db_query->select('*');
        
        $df = 'Y-m-d H:i:s';
        $size = 300;
        $query = $query->where('lastupdate >=',date($df,time()-86400));
        $query = $query->where('user_type',1);
        $query = $query->limit($size);
        $query = $query->get('t_user');
        //echo $this->db_query->last_query();
        if (!$query) {
            $e = $this->db_query->error();
            throw new Exception($e['message'], $e['code']);
        }
        return $query->result_array();
    }
    

    
}