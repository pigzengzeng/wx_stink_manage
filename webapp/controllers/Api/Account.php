<?php
class Account extends BaseApiController
{
	public function __construct(){
		parent::__construct();
        $this->load->model('account_model');
	}
	public function login(){
		
		$account_name = $this->input->post('account_name');
		$account_pwd = $this->input->post('account_pwd');

		if(empty($account_name) || empty($account_pwd)){
			$this->fail(ErrorCode::$ParamError,'参数错误');
		}
		
		$user = $this->account_model->get_account_by_name($account_name);

		if(empty($user)){
			$this->fail(ErrorCode::$IllegalUser,'用户不存在');
		}
		
		if($user['account_pwd']!=$account_pwd){//这里得搞个加密
			$this->fail(ErrorCode::$PwdError,'密码错误');
		}
		$account = array(
			'account_id'=>$user['pk_account'],
			'account_name'=>$user['account_name']
		);

		$this->session->set_userdata('account',$account);

		$this->success(1);

	}

	public function get_account(){
		if($this->check_login()){
			return $this->success($this->account);
		}

	}

	public function change_pwd(){
		$this->check_login();
		$account_id = $this->account['account_id'];
		$pwd = $this->input->post('pwd');
		$new_pwd = $this->input->post('new_pwd');

		$account = $this->account_model->get_account_by_id($account_id);
		if(empty($account)){
			$this->fail(ErrorCode::$IllegalUser,'用户不存在');
		}
		// echo $pwd,$new_pwd;
		// echo $this->secret_pwd($pwd);
		// echo "\n";
		// echo $account['account_pwd'];

		if($this->secret_pwd($pwd)!=$account['account_pwd']){
			$this->fail(ErrorCode::$IllegalUser,'密码不正确');
		}
		
		$affect = $this->account_model->update_pwd($account_id,$this->secret_pwd($new_pwd) ) ;
		$this->success(1);
		
	}

	private function secret_pwd($pwd){
		$r = md5('gx'.$pwd);
		return $r;
	}




	
}