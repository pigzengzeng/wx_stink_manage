<?php
class User extends BaseApiController
{
	public function __construct(){
		parent::__construct();
        $this->load->library('session');
        $this->load->library('retv');
        $this->load->model('user_model');
        $this->check_login();
	}
	public function user_state(){
		$userid = $this->input->get('userid');
		$state = (int)$this->input->get('state');
		
		if(empty($userid)){
			$this->fail(ErrorCode::$ParamError,'参数错误');
		}
		if(!in_array($state, array(0,1))){
			$this->fail(ErrorCode::$ParamError,'参数错误');
		}
		
		$user = $this->user_model->get_user_by_userid($userid);

		if(empty($user)){
			$this->fail(ErrorCode::$IllegalUser,'用户不存在');
		}

		try{
			$data['state'] = $state;
			$this->user_model->update_user($userid,$data);
			$this->success($state);

		}catch(Execapition $e){
			$this->fail(ErrorCode::$DBError,$e['message']);
		}

	}

	public function user_unbind_wgy(){
		$userid = $this->input->get('userid');
		
		if(empty($userid)){
			$this->fail(ErrorCode::$ParamError,'参数错误');
		}
		
		$user = $this->user_model->get_user_by_userid($userid);

		if(empty($user)){
			$this->fail(ErrorCode::$IllegalUser,'用户不存在');
		}

		try{
			if($user['user_type']==0){
				$user_type = 1;
			}else{
				$user_type = 0;
			}
			$data['user_type'] = $user_type;
			$this->user_model->update_user($userid,$data);
			$this->success($user_type);

		}catch(Execapition $e){
			$this->fail(ErrorCode::$DBError,$e['message']);
		}

	}

	public function get_user_last_position(){
		$users = $this->user_model->get_last_position();
		$this->success($users);
	}


	
}