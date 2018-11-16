<?php
class Marker extends BasePageController
{
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->config('res');

		$this->check_login();
        

	}
	public function marker_map(){
		$data['current_url']='/marker/marker_map';
		$data['res_request_url']=$this->config->item('res_request_url');
		

		$this->load->view("marker_map",$data);
	}
	public function login(){
		$this->load->view("login");
	}
}