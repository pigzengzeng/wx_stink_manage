<?php

class BasePageController extends CI_Controller {

    protected $account = null;
    protected $login_url = "/account/login";

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        if(!empty($this->session->account) ){
        	$this->account = $this->session->account;
    	}
        
    }


    protected function check_login(){
        if(empty($this->account)){
            //$back_url = current_url();
            $back_url = "/".uri_string();
            if(!empty($_SERVER['QUERY_STRING'])){

                $back_url.='?'.$_SERVER['QUERY_STRING'];
            }

        	$this->redirect($this->login_url."?backurl=".urlencode($back_url));
        	return ;
        }
        return true;
	}

	protected function redirect($location=''){
        if(!empty($location)){
            header('Location: ' . $location, true, 302);
        }
        return $this;
    }
    protected function clean_session() {
    	$this->session->unset_userdata('account');
    	$this->account=null;
    }


	protected function _display($error){


	}



  



}