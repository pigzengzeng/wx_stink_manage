<?php
class Account extends BasePageController
{
	public function __construct(){
		parent::__construct();
		$this->load->model('status_model');
	}
	public function index(){
		$this->check_login();		
		$data['current_url']='/account/index';
		$this->load->view("index",$data);
	}
	public function login(){
		$this->load->view("login");
	}
	public function logout(){
		$this->clean_session();
		$this->redirect($this->login_url);

	}
	public function change_pwd(){
		$data['current_url']='/account/change_pwd';
		$this->load->view("account_change_pwd",$data);
	}
	
}