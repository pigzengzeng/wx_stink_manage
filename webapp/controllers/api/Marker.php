<?php
class Marker extends BaseApiController
{
	private $city = '';
	public function __construct(){
		parent::__construct();
        $this->load->model('marker_model');
        $this->load->model('marker_photo_model');
        $this->load->model('user_model');
        $this->load->model('res_model');
        
        $this->check_login();

        $this->city = empty($this->account['city'])?'':$this->account['city'];
	}
	public function get_markers(){
		
		
		$x1 = $this->input->get('x1');
		$y1 = $this->input->get('y1');
		$x2 = $this->input->get('x2');
		$y2 = $this->input->get('y2');


		$time_from = (int)$this->input->get('time_from');
		$time_to = (int)$this->input->get('time_to');


		$df = 'Y-m-d H:i:s';
		if(!empty($time_from)){
			$time_from = date($df,$time_from);	
		}
		if(!empty($time_to)){
			$time_to = date($df,$time_to);	
		}

		//echo $time_from,"-",$time_to;
		

		$marker_level = $this->input->get('marker_level');
		if($marker_level!=''){
			$level_array = explode(',', $marker_level);
		}else{
			$level_array = [];
		}
		
		try{
			$markers = $this->marker_model->get_markers($x1,$y1,$x2,$y2,$level_array,$time_from,$time_to,$this->city);
		}catch (Exception $e){			
			$this->fail(ErrorCode::$DBError,$e['message']);
		}

		$data = array();
		foreach ($markers as $item){
			$marker = [];
			$marker['id'] = (int)$item['pk_marker'];
			
			$marker['latitude'] = (double)$item['latitude'];
			$marker['longitude'] = (double)$item['longitude'];
			$marker['odour'] = $item['odour'];
			$marker['intensity'] = $item['intensity'];

			$marker['level'] = $item['level'];

			$marker['createtime'] = $item['createtime'];
			$marker['lastupdate'] = $item['lastupdate'];
			$marker['userid'] = $item['fk_user'];
			$data['markers'][]=$marker;
			
		}
		$this->success($data);

	}




	public function get_marker(){
		$markerid = $this->input->get('markerid');
		if(empty($markerid)){
			$this->fail(ErrorCode::$ParamError,'参数不全');
		}
		
		$marker = $this->marker_model->get_marker_by_id($markerid);
		if(empty($marker)){
			$this->fail(ErrorCode::$ParamError,'markerId 没找到');
		}

		$markerInfo['markerId'] = $marker['pk_marker'];
		$markerInfo['longitude'] = (double)$marker['longitude'];
		$markerInfo['latitude'] = (double)$marker['latitude'];
		$markerInfo['odour'] = (int)$marker['odour'];
		$markerInfo['intensity'] = (int)$marker['intensity'];
		$markerInfo['remark'] = $marker['remark'];
		$markerInfo['state'] = (int)$marker['state'];
		$markerInfo['createtime'] = $marker['createtime'];
		$markerInfo['user']['id'] = (int)$marker['fk_user'];
		$markerInfo['photos'] = array();
		$user = $this->user_model->get_user_by_userid($marker['fk_user']);
		if(!empty($user)){
			$markerInfo['user']['nickname'] = $user['nickname'];
			$markerInfo['user']['realname'] = $user['realname'];
			$markerInfo['user']['user_type'] = $user['user_type'];
		}
		
		$photos = $this->marker_photo_model->get_photos_by_markerid($markerid);
		if(!empty($photos)){
			$file_name_keys = array();
			foreach ($photos as  $photo) {
				$file_name_keys[] = $photo['file_name_key'];
			}
			$files = $this->res_model->get_files_by_key($file_name_keys);
			if(!empty($files)){
				foreach ($files as $file) {
					$markerInfo['photo_files'][] = $file['orig_name'] ;
				}
			}
		}
		$this->success($markerInfo);
		
	}

	


	
}