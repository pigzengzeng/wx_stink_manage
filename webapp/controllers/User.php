<?php
class User extends BasePageController
{
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->check_login();

		$this->load->model('user_model');


	}
	public function userlist(){
		$data['current_url']='/user/userlist';
		
		$page = $this->input->get('per_page');
		$size = 10;


		$keyword = $this->input->get('keyword');
		$user_type = $this->input->get('user_type');
		$state = $this->input->get('state');

		$order_field = $this->input->get('order_field');
		$order = $this->input->get('order');

		$data['keyword'] = $keyword;
		$data['user_type'] = $user_type;
		$data['state'] = $state;
		$data['order_field'] = $order_field;
		$data['order'] = $order;


		if(empty($keyword))$keyword='';

		if(empty($user_type)){
			$user_type_array = array();
		}else{
			$user_type_array = explode(',', $user_type);
		}
		if(empty($state)){
			$state_array = array();
		}else{
			$state_array = explode(',', $state);
		}


		
		$order_field = empty($order_field)?"createtime":$order_field;
		$order = $order=='asc'?'asc':'desc';


		

		$total = $this->user_model->get_total($keyword,$user_type_array,$state_array);
		$total_page = ceil($total/$size) ;

		if( intval($page) < 1 ) $page=1;
		if( $page > ceil($total_page) ){
			$page = ceil($total/$size);
		}

		$users = array();
		if(!empty($total)){
			$users = $this->user_model->search($keyword,$user_type_array,$state_array,$page,$size,$order_field,$order);
			
		}
		$data['total'] = $total;
		$data['total_page'] = $total_page;
		$data['page'] = $page;
		$data['users'] = $users;


		
		//print_r($users);
		$config = array();
		$this->load->library('pagination');
		$config['base_url'] = '/user/userlist?keyword='.urlencode($keyword);
		$config['total_rows'] = $total;
		$config['per_page'] = 10;
		$config['num_links'] = 3;
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['enable_query_strings'] = TRUE;

		$config['first_link'] = '第一页';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';


		$config['last_link'] = '最末页';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		$config['prev_link'] = '上一页';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';


		$config['next_link'] = '下一页';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';

		
		$config['cur_tag_open'] = '<li class="active"><a>';
		$config['cur_tag_close'] = '</a></li>';

		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';


		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();
		

		$this->load->view("userlist",$data);
	}
	public function login(){
		$this->load->view("login");
	}
}