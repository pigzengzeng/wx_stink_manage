<?php
class Status extends BaseApiController
{
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
	private $city = '';
	public function __construct(){
		parent::__construct();
        $this->load->model('status_model');
        $this->check_login();        
        $this->city = empty($this->account['city'])?'':$this->account['city'];
	}
	public function get_status_for_odour_intensity(){
		$dt = $this->input->get("dt");
		$city = $this->input->get("city");
		

		if($dt=='d' ||empty($dt)){
			$from_ctime=date("Y-m-d H:i:s",time()-86400);
			$to_ctime=date("Y-m-d H:i:s",time());
		}
		if($dt=='w'){
			$from_ctime=date("Y-m-d H:i:s",time()-86400*7);
			$to_ctime=date("Y-m-d H:i:s",time());
		}
		if($dt=='m'){
			$from_ctime=date("Y-m-d H:i:s",time()-86400*30);
			$to_ctime=date("Y-m-d H:i:s",time());
		}
		$from_ctime = date("Y-m-d H:i:s",time()-86400*365);

		if(!empty($this->city)){
			$city = $this->city;
		}

		$data = $this->status_model->get_status_for_odour_intensity($from_ctime,$to_ctime,$city);
		$r=[];
		if(!empty($data['aggregations']['odour_group']['buckets'])){
			$odours = $data['aggregations']['odour_group']['buckets'];
			foreach ($odours as $item) {
				$r['odours']['titles'][] = $this->odours[$item['key']];
				$r['odours']['values'][] = $item['doc_count'];
			}
		}
		if(!empty($data['aggregations']['intensity_group']['buckets'])){
			$odours = $data['aggregations']['intensity_group']['buckets'];
			foreach ($odours as $item) {
				$r['intensities']['titles'][] = $this->intensities[$item['key']];
				$r['intensities']['values'][] = $item['doc_count'];
			}
		}

		$this->success($r);

	}

	public function get_status_cities(){
		if(!empty($this->city)){
			$city = $this->city;
			$this->success($city);
		}

		$dt = $this->input->get("dt");		
		if($dt=='d' ||empty($dt)){
			$from_ctime=date("Y-m-d H:i:s",time()-86400);
			$to_ctime=date("Y-m-d H:i:s",time());
		}
		if($dt=='w'){
			$from_ctime=date("Y-m-d H:i:s",time()-86400*7);
			$to_ctime=date("Y-m-d H:i:s",time());
		}
		if($dt=='m'){
			$from_ctime=date("Y-m-d H:i:s",time()-86400*30);
			$to_ctime=date("Y-m-d H:i:s",time());
		}
		$from_ctime="2019-01-01 00:00:00";
		$to_ctime=date("Y-m-d H:i:s",time());
		$r = $this->status_model->get_status_cities($from_ctime,$to_ctime);

		$this->success($r);

	}
	public function get_status_for_district(){
		$dt = $this->input->get("dt");
		$city = $this->input->get("city");
		if(!empty($this->city)){
			$city = $this->city;
		}
		if($dt=='d' ||empty($dt)){
			$from_ctime=date("Y-m-d H:i:s",time()-86400);
			$to_ctime=date("Y-m-d H:i:s",time());
		}
		if($dt=='w'){
			$from_ctime=date("Y-m-d H:i:s",time()-86400*7);
			$to_ctime=date("Y-m-d H:i:s",time());
		}
		if($dt=='m'){
			$from_ctime=date("Y-m-d H:i:s",time()-86400*30);
			$to_ctime=date("Y-m-d H:i:s",time());
		}

		
		
		$r = $this->status_model->get_status_for_district($from_ctime,$to_ctime,$city);

		
		
		$this->success($r);
	}

	
}