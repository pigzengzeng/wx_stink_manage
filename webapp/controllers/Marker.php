<?php
class Marker extends BasePageController
{
	public function __construct(){
		parent::__construct();
		$this->load->library('curl');
		$this->load->config('res');
		$this->load->model('marker_model');
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

	public function sync_area(){
		$items = $this->marker_model->search([]);
		$counter = 0;
		foreach ($items as $item) {
			$lon = $item['longitude'];
			$lat = $item['latitude'];

			$params = [
				"key"=>"7de23dce7da811e6cac92d926932a1f9",
				"location"=>"$lon,$lat"
			];
			$url = "https://restapi.amap.com/v3/geocode/regeo";
			$data = $this->curl->simple_get($url,$params);	
			$data = json_decode($data);
			
			$province = empty($data->regeocode->addressComponent->province)?'':$data->regeocode->addressComponent->province;
			$city = empty($data->regeocode->addressComponent->city)?'':$data->regeocode->addressComponent->city;
			if(empty($city))$city=$province;
			$district = empty($data->regeocode->addressComponent->district)?'':$data->regeocode->addressComponent->district;
			echo "$lon,$lat:$province $city $district \n";
			$this->marker_model->update_marker($item['pk_marker'],[
				"province"=>$province,
				"city"=>$city,
				"district"=>$district
			]);
			flush();
		}
	}
	public function make_test_data(){
		$items = $this->marker_model->search([]);
		$counter = 0;
		foreach ($items as &$item) {
			unset($item['pk_marker']);
			$item['createtime'] = date("Y-m-d H:i:s",time()-mt_rand(0,86400*7));
			$this->marker_model->insert_marker($item);
			$counter++;
			print_r($item);
			flush();
		}
	}




}