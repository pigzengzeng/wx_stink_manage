<?php
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Message extends CI_Controller
{	
	private $last_utime_filepath = '/tmp/';
	private $odours = [
			'1'=>'农药化工味',
			'2'=>'臭鸡蛋味',
			'3'=>'臭鱼烂肉味',
			'4'=>'烂白菜味',
			'5'=>'油脂薰蒸味',
			'99'=>'难以辨别'
	];
	private $intensities = [
		'1'=>'轻微',
		'2'=>'一般',
		'3'=>'强烈',
		'4'=>'难忍'
	];
	public function __construct(){
		parent::__construct();
		$this->load->library('curl');
		$this->load->helper('file');
        $this->load->model('conf_model');
        $this->load->model('marker_model');
        $this->load->model('account_model');

        if (!is_cli()){
	        echo 'This script is only run in cli mode.';
	        exit();
	    }
	}
	public function watcher(){
		$last_utime_filepath = '/tmp/watcher_'.md5(__Class__);

		$utime = @file_get_contents($last_utime_filepath);
		if(empty($utime)){
			echo "Not found last utime file path.\n";
			$utime = time()-86400;
		}
		while (true) {			
			$rows = $this->marker_model->get_markers_for_lastutime($utime);
			if(empty($rows)){
				//echo "No marker.wait...\n";
				sleep(3);
				continue;
			}
			$utime = strtotime($rows[0]['lastupdate']);
			if(!write_file($last_utime_filepath, $utime)){
				echo 'Unable to write the '.$last_utime_filepath;
				exit();
			}
			foreach ($rows as $marker) {				
				$task = $this->get_message_task($marker);				
				if(empty($task))continue;				
				$this->send_message($task);
			}
		}
		

	}
	private function get_message_task($marker){
		$city = $marker['city'];
		if(empty($city))return null;		
		$customers = $this->account_model->get_account_by_city($city);
		if(empty($customers))return null;
		$tasks = [];		
		foreach ($customers as $customer) {
			$account_id = $customer['pk_account'];
			$access_key_id = empty($customer['access_key_id'])?'':$customer['access_key_id'];
			$access_key_secret = empty($customer['access_key_secret'])?'':$customer['access_key_secret'];
			if(empty($account_id)||empty($access_key_secret))continue;

			$conf_messages = $this->conf_model->get_conf_messages_by_account_id($account_id);						
			if(empty($conf_messages))continue;

			$tels = [];
			foreach ($conf_messages as $item) {
				if($marker['intensity']<$item['intensity'])continue;
				$tels = explode(',',$item['tel']);
			}
			array_unique($tels);
			if(empty($tels))continue;

			$tasks[] = [
				'access_key'=>['AccessKeyId'=>$access_key_id,'AccessKeySecret'=>$access_key_secret],
				'tels'=>$tels
			];			
		}
		if(empty($tasks))return null;
		
		$r['tasks']=$tasks;

		//发送的信息
		$lon = $marker['longitude'];
		$lat = $marker['latitude'];
		$params = [
			"key"=>"7de23dce7da811e6cac92d926932a1f9",
			"location"=>"$lon,$lat"
		];
		$url = "https://restapi.amap.com/v3/geocode/regeo";
		$data = $this->curl->simple_get($url,$params);	
		$data = json_decode($data);		
		$address =  !empty($data->regeocode->addressComponent->district)?$data->regeocode->addressComponent->district:'';
		$address .= !empty($data->regeocode->addressComponent->township)?$data->regeocode->addressComponent->township:'';
		$address .= !empty($data->regeocode->addressComponent->streetNumber->street)?$data->regeocode->addressComponent->streetNumber->street:'';
		$address .= !empty($data->regeocode->addressComponent->streetNumber->number)?$data->regeocode->addressComponent->streetNumber->number:'';		
		$address = mb_substr($address,0,20); //短信要求变量少于20个字符
		

		
		//太长了
		//$address = empty($data->regeocode->formatted_address)?'':$data->regeocode->formatted_address;
		if(!empty($address)){
			
			if(!empty($this->intensities[$marker['intensity']])){
				$intensity = $this->intensities[$marker['intensity']];
			}else{
				$intensity = '';
			}
			if(!empty($this->odours[$marker['odour']])){
				$odour = $this->odours[$marker['odour']];
			}else{
				$odour = '';
			}
			$r['template_params'] = [
				'address'=>$address,
				'intensity'=>$intensity,
				'odour'=>$odour
			];
		}else{
			return null;
		}		
		
		return $r;
	}

	private function send_message($send_tasks){		
		foreach ($send_tasks['tasks'] as $task ) {
			$tel_count = count($task['tels']);
			$phone_number_json = json_encode($task['tels']);
			$sign_name_json = json_encode( array_fill(0,$tel_count,'秀嗅提醒') );
			$template_param_json = json_encode( array_fill(0,$tel_count,$send_tasks['template_params']) );
			
			$access_key_id = $task['access_key']['AccessKeyId'];
			$access_key_secret = $task['access_key']['AccessKeySecret'];

			$params = [
				'access_key_id'=>$access_key_id,
				'access_key_secret'=>$access_key_secret,
				'phone_number_json'=>$phone_number_json,				
				'sign_name_json'=>$sign_name_json,
				'template_param_json'=>$template_param_json
			];
			print_r([
				'access_key_id'=>$access_key_id,				
				'phone_number_json'=>json_decode($phone_number_json),
				'sign_name_json'=>json_decode($sign_name_json),
				'template_param_json'=>json_decode($template_param_json)
			]);
			$this->aliyun_send_message($params);
			
		}
		
	}

	private function  aliyun_send_message($data){
		AlibabaCloud::accessKeyClient($data['access_key_id'], $data['access_key_secret'])
                        ->regionId('cn-hangzhou')
                        ->asDefaultClient();

		try {
		    $result = AlibabaCloud::rpc()
		                          ->product('Dysmsapi')
		                          // ->scheme('https') // https | http
		                          ->version('2017-05-25')
		                          ->action('SendBatchSms')
		                          ->method('POST')
		                          ->host('dysmsapi.aliyuncs.com')
		                          ->options([
		                                        'query' => [
		                                          'RegionId' => "cn-hangzhou",
		                                          'PhoneNumberJson' => $data['phone_number_json'],
		                                          'SignNameJson' => $data['sign_name_json'],
		                                          'TemplateCode' => "SMS_181211378",
		                                          'TemplateParamJson' => $data['template_param_json'],
		                                        ],
		                                    ])
		                          ->request();
		    print_r($result->toArray());
		} catch (ClientException $e) {
		    echo $e->getErrorMessage() . PHP_EOL;
		} catch (ServerException $e) {
		    echo $e->getErrorMessage() . PHP_EOL;
		}

	}
	
}