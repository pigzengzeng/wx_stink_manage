<?php

class Marker_model extends CI_model
{
    private $db_main  = null;
    private $db_query = null;
	
    public function __construct() {
        parent::__construct();

        $this->db_main = $this->load->database('db_main',TRUE, true);
        $this->db_query = $this->load->database('db_query', TRUE, true);

        $this->load->config('elastic');
        
    }
	/**
	 * 通过markerid，获取一个marker点
	 * @param unknown $markerid
	 */
    public function get_marker_by_id($markerid){
    	if (!$query = $this->db_query->get_where('t_marker', ['pk_marker'=>$markerid])) {
    		$e = $this->db_query->error();
    		throw new Exception($e['message'], $e['code']);
    	}
    	return $query->row_array();
    }
    
    
    
	/**
	 * 获取多个marker点
	 */
    
    public function get_markers($x1,$y1,$x2,$y2,$level,$time_from,$time_to,$city=''){
    	
    	/*ES取数据
    	 * 
    	 */
    	//初始化ES_Client
    	$client = Elasticsearch\ClientBuilder::create()->setHosts($this->config->item('hosts'))->setRetries(2)->build();

    	
    	$r=array();
    	$params = [
			'index'=>'markers',
    		'type' => 'test',
    		'body' => [
    			'query'=>[
					'bool'=>[
						'filter' =>[
							['term'=>['state'=>0]],
							['geo_bounding_box' =>[
									'location' =>[
											'top_right'=>[
													'lat'=> doubleval($y1),
													'lon'=> doubleval($x1)
											],
											'bottom_left'=>[
													'lat'=> doubleval($y2),
													'lon'=> doubleval($x2)
											]
									]
							]]
						]
					]
    			],
                'size'=>500
    		]
		];

        if(count($level)>0){
            $level_params = [];
            if(in_array(0, $level)){
                $level_params['bool']['should'][] = ['term'=>['level'=>0]];
            }
            if(in_array(1, $level)){
                $level_params['bool']['should'][] = ['term'=>['level'=>1]];
            }
            $params['body']['query']['bool']['filter'][] = $level_params;

        }else{
            
            $level_params['bool']['must'][] = ['term'=>['level'=>99]];
            
            $params['body']['query']['bool']['filter'][] = $level_params;

        }

        //$params['body']=array();
        $must = [];
        if(!empty($time_from)){
            $must[0]['range']['createtime']['gte'] = $time_from;
            //$params['body']['query']['range']['lastupdate']['gte'] = $time_from;
        }
        
        if(!empty($time_to)){
            $must[0]['range']['createtime']['lte'] = $time_to;
        }
        
        if(!empty($city)){
            $must[1] = ['term'=>['city'=>$city]];
        }
        $params['body']['query']['bool']['must'] = $must;


        if(!empty($userid)){
            $params['body']['query']['bool']['filter'][]['term']['fk_user'] = $userid;
            
        }


	    //print_r($params);
	    try{
	    	$markers = $client->search($params);
	    }catch (Exception $e){
	    	throw $e;
	    }
    	
        
    	if(empty($markers['hits']['hits'])){
            
    		return $r; 
    	}

    	foreach ($markers['hits']['hits'] as $marker) {
            $item=array();
            $item['pk_marker'] = $marker['_source']['pk_marker'];
            $item['longitude'] = $marker['_source']['longitude'];
            $item['latitude'] = $marker['_source']['latitude'];
            $item['odour'] = $marker['_source']['odour'];
            $item['intensity'] = $marker['_source']['intensity'];
            $item['level'] = $marker['_source']['level'];
            $item['fk_user'] = $marker['_source']['fk_user'];
            $item['state'] = $marker['_source']['state'];
            $item['createtime'] = $marker['_source']['createtime'];
            $item['lastupdate'] = $marker['_source']['lastupdate'];
            $r[] = $item;
        }
        return $r;
    }
    
    
    /**
     * 删除marker，不真删除，使state=1
     *
     **/
    public function delete_marker($markerid) {
    	
    	$fields = array();
    	$fields['state'] = self::STATE_DELETED;
    	if(!$this->db_main->set($fields)->where('pk_marker', $markerid)->update('t_marker')) {
    		$error = $this->db_main->error();
    		throw new Exception($error['message'], $error['code']);
    	}
    	$affected_rows = $this->db_main->affected_rows();
    	return $affected_rows;
    }
    
    public function  search($data){
       
        
        $size =1000 ;

        $this->db_query->select('*');
        $this->db_query->from('t_marker');
        $this->db_query->where('state',0);
        $this->db_query->order_by('pk_marker','desc');
        $this->db_query->limit($size);
        
        if(!$query = $this->db_query->get()){
            $e = $this->db_query->error();
            throw new Exception($e['message'], $e['code']);
        }
        return $query->result_array();


    }
    public function insert_marker($data){
        $this->db_main->insert('t_marker',$data);
        $insert_id = $this->db_main->insert_id();        
    }

    public function update_marker($marker_id,$fields){
        if(empty($fields)) return 0;
        
        if(!$this->db_main->set($fields)->
                where('pk_marker', $marker_id)->
                update('t_marker')) {
            $error = $this->db_main->error();
            throw new Exception($error['message'], $error['code']);
        }
        $affected_rows = $this->db_main->affected_rows();
        return $affected_rows;        
    }



}
