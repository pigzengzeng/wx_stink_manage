<?php

class Status_model extends CI_model
{
    private $db_main  = null;
    private $db_query = null;	
    private $client = null;
    public function __construct() {
        parent::__construct();
        $this->load->library("curl");
        $this->db_main = $this->load->database('db_main',TRUE, true);
        $this->db_query = $this->load->database('db_query', TRUE, true);
        $this->load->config('elastic');

        $this->client = Elasticsearch\ClientBuilder::create()->setHosts($this->config->item('hosts'))->setRetries(2)->build();
        
    }
	
    public function get_status_for_odour_intensity($from_ctime,$to_ctime,$city=''){
        $r=array();
        $params = [
            'index'=>'markers',
            'type' => 'test',
            'body' => [
                'query'=>[                    
                    'bool'=>[                        
                        'must'=>[                            
                            ['range'=>[
                                'createtime'=>['gte'=>$from_ctime,'lte'=>$to_ctime]
                            ]]
                        ],
                        'filter' =>[
                            ['term'=>['state'=>0]]
                        ]
                    ]
                ],
                'aggs'=>[
                    'odour_group'=>[
                        'terms'=>[
                            'field'=>'odour'
                        ]
                    ],
                    'intensity_group'=>[
                        'terms'=>[
                            'field'=>'intensity'
                        ]
                    ]
                ]
            ]
        ];        
        if(!empty($city)){
            $params['body']['query']['bool']['must'][] = ['term'=>['city'=>$city]];
        }
        
        try{
            $r = $this->client->search($params);
        }catch (Exception $e){
            throw $e;
        }
        //print_r($params);
        return $r;
    }
    

    public function get_status_cities($from_ctime,$to_ctime,$city=''){
        $r=array();
        $params = [
            'index'=>'markers',
            'type' => 'test',
            'body' => [
                'query'=>[
                    'bool'=>[
                        'must'=>[
                            ['range'=>[
                                'createtime'=>['gte'=>$from_ctime,'lte'=>$to_ctime]                                
                            ]]
                        ],                        
                        'filter' =>[
                             ['term'=>['state'=>0]]     
                        ]
                    ]
                ],
                'aggs'=>[
                    'cities'=>[
                        'terms'=>[
                            'field'=>'city'
                        ]
                    ]
                ]
            ]
        ];        
        //print_r($params);
        if(!empty($city)){
            $params['body']['query']['bool']['must'][] = ['term'=>['city'=>$city]];
        }
        try{
            $data = $this->client->search($params);
        }catch (Exception $e){
            throw $e;
        }
        $r=[];
        if(!empty($data['aggregations']['cities']['buckets'])){
            $cities = $data['aggregations']['cities']['buckets'];
            foreach ($cities as $item) {
                if(empty($item['key'])){
                    $r[] = [
                        'city' => '未知',
                        'count'=> $item['doc_count']
                    ];    
                }else{
                    $r[] = [
                        'city' => $item['key'],
                        'count'=> $item['doc_count']
                    ];    
                }
                
            }
        }
        //print_r($params);
        return $r;
    }

    public function get_status_for_district($from_ctime,$to_ctime,$city=''){
        $r=array();
        $params = [
            'index'=>'markers',
            'type' => 'test',
            'body' => [
                'query'=>[
                    'bool'=>[
                        'must'=>[
                            ['range'=>[
                                'createtime'=>['gte'=>$from_ctime,'lte'=>$to_ctime]                                
                            ]]
                        ],                        
                        'filter' =>[
                             ['term'=>['state'=>0]]     
                        ]
                    ]
                ],
                'aggs'=>[
                    'districtes'=>[
                        'terms'=>[
                            'field'=>'district'                            
                        ]
                    ],

                ]
            ]
        ];
        if(!empty($city)){
            $params['body']['query']['bool']['must'][] = ['term'=>['city'=>$city]];
        }
        
        
        try{
            $data = $this->client->search($params);
        }catch (Exception $e){
            throw $e;
        }
        if(!empty($data['aggregations']['districtes']['buckets'])){
            $cities = $data['aggregations']['districtes']['buckets'];
            foreach ($cities as $item) {
                if(empty($item['key'])){
                    $r[] = [
                        'district'=>'未知',
                        'count'=>$item['doc_count']
                    ];
                }else{
                    $r[] = [
                        'district'=>$item['key'],
                        'count'=>$item['doc_count']
                    ];    
                }
                
            }
        }


        //print_r($params);
        return $r;
    }
    
   


}
