<?php
class Conf extends BaseApiController
{	
	public function __construct(){
		parent::__construct();
        $this->load->model('conf_model');
        $this->check_login();        
	}
	public function get_conf_message(){		
		$account_id = $this->account['account_id'];		
		$r = null;
		try{
			$conf_message = $this->conf_model->get_conf_message_by_account_id($account_id);
			if(!empty($conf_message)){
				$r = [
					'conf_message_id'=>(int)$conf_message['pk_conf_message'],
					'account_id'=>(int)$conf_message['fk_account'],
					'tel'=>$conf_message['tel'],
					'intensity'=>(int)$conf_message['intensity'],
					'access_key_id'=>$conf_message['access_key_id'],
					'access_key_secret'=>$conf_message['access_key_secret'],
					'sign_name'=>$conf_message['sign_name'],
					'template_code'=>$conf_message['template_code']
				];
			}
		}catch (Exception $e){			
			$this->fail(ErrorCode::$DBError,$e['message']);
		}

		$this->success($r);

		
		

	}
	public function save_conf_message(){
		$account_id = $this->account['account_id'];
		$conf_message = $this->conf_model->get_conf_message_by_account_id($account_id);
		$intensity = (int)$this->input->post('intensity');
		$tel = $this->input->post('tel');

		$access_key_id = $this->input->post('access_key_id');
		$access_key_secret = $this->input->post('access_key_secret');
		$sign_name = $this->input->post('sign_name');
		$template_code = $this->input->post('template_code');


		if(empty($conf_message)){
			$data = [
				'fk_account'=>$account_id,
				'intensity'=>$intensity,
				'tel'=>$tel,
				'access_key_id'=>$access_key_id,
				'access_key_secret'=>$access_key_secret,
				'sign_name'=>$sign_name,
				'template_code'=>$template_code
			];
			$conf_message_id = $this->conf_model->insert_conf_message($data);
			$r['conf_message_id'] = $conf_message_id;
		}else{
			$conf_message_id = (int)$conf_message['pk_conf_message'];
			$data = [				
				'intensity'=>$intensity,
				'tel'=>$tel,
				'access_key_id'=>$access_key_id,
				'access_key_secret'=>$access_key_secret,
				'sign_name'=>$sign_name,
				'template_code'=>$template_code
			];
			$affect = $this->conf_model->update_conf_message($conf_message_id,$data);			
			$r['conf_message_id'] = $conf_message_id;
			$r['affect'] = $affect;
		}
		$this->success($r);
	}

}