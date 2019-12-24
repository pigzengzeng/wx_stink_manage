<?php
class Conf extends BasePageController
{
	public function __construct(){
		parent::__construct();		
		$this->check_login();		
	}
	
	public function message(){
		$data['current_url']='/conf/message';
		$this->load->view("message",$data);
	}
}