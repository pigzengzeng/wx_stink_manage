<?php

class BaseApiController extends CI_Controller {
    private $_start_time;
    protected $account = null;

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('account_model');
        $BM =& load_class('Benchmark', 'core');
        $this->_start_time = $BM->marker['total_execution_time_start'];

        if(!empty($this->session->account) ){
            $this->account = $this->session->account;
        }

    }

    protected function check_login(){
        if(empty($this->account)){
        	$this->fail(-403);
        }
        return true;
    }

  
    protected function response($data){
        echo json_encode($data);
        exit();
    }


    protected function success($data){
        $res = array(
            'error'     => 0,
            'message'   => 'success',
            'cost'      => (microtime(true) - $this->_start_time)*1000 . 'ms',
            'result'    => $data
        );
        $this->response($res);
        exit();
    }

    protected function fail($errorcode=-9999, $message='unknown error.', $result = null){
        $res = array(
            'error'     => $errorcode,
            'message'   => $message,
            'cost'      => (microtime(true) - $this->_start_time)*1000 . 'ms',
            'result'    => !empty($result) ? $result : null
        );
        $this->response($res);
        die();
    }





}