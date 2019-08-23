<!DOCTYPE html>
<html lang="en">
<head>
<title>恶臭小程序管理后台</title>
<?php require_once('global_css.php')?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/4.2.1/echarts.min.js"></script>
<style type="text/css">
	#tab_charts {

	}
</style>
</head>
<body>

<?php require_once('top_header.php')?>
<?php require_once('sidebar_menu.php')?>

<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="#" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> 首页</a></div>
  </div>
<!--End-breadcrumbs-->
  <div class="container-fluid">
  <table id="tab_charts">
  
  	<tr><td colspan="3">
  		选择城市：<select id="select_city"></select>
  	</td>
  	
  	</tr>
  	<tr>  		
  		<td><div id="chart_day_odour"  style="width: 300px;height:200px;background: #EEEEEE"></div></td>
  		<td><div id="chart_day_intensity"  style="width: 300px;height:200px;background: #EEEEEE"></div></td>
  		<td><div id="chart_day_city"  style="width: 300px;height:200px;background: #EEEEEE"></div></td>
  	</tr>
  	<tr>  		
  		<td><div id="chart_week_odour"  style="width: 300px;height:200px;background: #EEEEEE"></div></td>
  		<td><div id="chart_week_intensity"  style="width: 300px;height:200px;background: #EEEEEE"></div></td>
  		<td><div id="chart_week_city"  style="width: 300px;height:200px;background: #EEEEEE"></div></td>
  	</tr>
  	<tr>  		
  		<td><div id="chart_month_odour"  style="width: 300px;height:200px;background: #EEEEEE"></div></td>
  		<td><div id="chart_month_intensity"  style="width: 300px;height:200px;background: #EEEEEE"></div></td>
  		<td><div id="chart_month_city"  style="width: 300px;height:200px;background: #EEEEEE"></div></td>
  	</tr>

  </table>
  </div>

</div>
<!--end-main-container-part-->
<script type="text/javascript">
__hook_funs.push(function(){
	$("#select_city").change(function(){
		refresh_chart();
	})

	init_city(function(){
		refresh_chart();
	});
});

function refresh_chart(){
	let city = $("#select_city").val();
	show_odour_intensity('d',city);
	show_odour_intensity('w',city);
	show_odour_intensity('m',city);	

	show_district('d',city);
	show_district('w',city);
	show_district('m',city);	


}
function init_city(cb){
	$.ajax({
			url:'/api/status/get_status_cities',
			type:"get",
	      	dataType:"json",
			success:function (res){	
				if(!res.result)return;
				let cities = res.result;
				cities.forEach(function(city){
					let opt = document.createElement('option');
					$(opt).prop('value',city.city);					
					$(opt).text(city.city);
					if(city.city=='日照市'){
						$(opt).prop('selected',true);					
					}
					$("#select_city").append(opt);					
				})
				cb();
				
			}
		});
}


function show_district(dt,city){
	switch(dt){
		case 'd':
			chartName = 'chart_day_city';
			chartTitle = '按地区(24H)';
			break;
		case 'w':
			chartName = 'chart_week_city';
			chartTitle = '按地区(最近7天)';
			break;
		case 'm':
			chartName = 'chart_month_city';
			chartTitle = '按地区(最近30天)';
			break;					
	}
	let chart2 = echarts.init(document.getElementById(chartName));
	let option = {
		title : {
	        text: chartTitle,	        
	        x:'center'
	    },
	    color: ['#6699FF'],

	    grid: {
	        left: '3%',
	        right: '4%',
	        bottom: '3%',
	        containLabel: true
	    },
	    xAxis : [
	        {
	            type : 'category',
	            data : [],
	            axisTick: {	            	
	                alignWithLabel: true
	            },
	            axisLabel:{ //字斜过来
            		interval:0,  
                    rotate:45,
                    margin:10,
                    fontSize:14
	            }
	        }
	    ],
	    yAxis : [
	        {
	            type : 'value'
	        }
	    ],
	    series : [
	        {
	            name:'举报数量',
	            type:'bar',
	            barMaxWidth: '25px',
	            barCategoryGap:'5%',
	            data:[],
	            label: { //顶部显示数据
		    		show: true, //开启显示
		    		position: 'top', //在上方显示
		    		textStyle: { //数值样式
		    		    color: 'white',
		    			fontSize: 12		    					    			
		    		},
		    		backgroundColor:"#6699FF",
		    		
		    		width:'100px',		    		
		    		
		    	}
			     

	        }
	    ]
	};

	$.ajax({		
		url:'/api/status/get_status_for_district?dt='+dt+'&city='+city,
		type:"get",
      	dataType:"json",
		success:function (res){			
			var yMax = 0;
			for(let i=0;i<res.result.length;i++){
				option.xAxis[0].data.push(res.result[i].district);
				option.series[0].data.push(res.result[i].count);				
			}

			chart2.setOption(option);
		}
	});

}

function show_odour_intensity(dt,city){
		$.ajax({
			url:'/api/status/get_status_for_odour_intensity?dt='+dt+'&city='+city,
			type:"get",
	      	dataType:"json",
			success:function (res){			
				let chartName = '';
				let chartTitle = '';
				let odours=[];
				let intensities=[];
				
				let titles =  [];
				let values =  [];	
				if(res.result){
					if(res.result.odours){
						titles = res.result.odours.titles || [];
						values = res.result.odours.values || [];
					}	
				}
				
				for(let i=0;i<titles.length;i++){
					//option.legend.data.push(titles[i]); //图例
					odours.push({
						value:values[i],
						name:titles[i]
					});
				}
				switch(dt){
					case 'd':
						chartName = 'chart_day_odour';
						chartTitle = '按气味类型(24H)';
						break;
					case 'w':
						chartName = 'chart_week_odour';
						chartTitle = '按气味类型(最近7天)';
						break;
					case 'm':
						chartName = 'chart_month_odour';
						chartTitle = '按气味类型(最近30天)';
						break;					
				}
				show_pie(chartName,chartTitle,odours);
				
				titles =  [];
				values =  [];	
				if(res.result){
					if(res.result.intensities){
						titles = res.result.intensities.titles || [];
						values = res.result.intensities.values || [];	
					}
					
				}
				for(let i=0;i<titles.length;i++){
					//option.legend.data.push(titles[i]); //图例
					intensities.push({
						value:values[i],
						name:titles[i]
					});
				}

				switch(dt){
					case 'd':
						chartName = 'chart_day_intensity';
						chartTitle = '按强度(24H)';
						break;
					case 'w':
						chartName = 'chart_week_intensity';
						chartTitle = '按强度(最近7天)';
						break;
					case 'm':
						chartName = 'chart_month_intensity';
						chartTitle = '按强度(最近30天)';
						break;					
				}				
				show_pie(chartName,chartTitle,intensities);
				
			}
		});	
	
	
}
function show_pie(chart,title,data){
	let chart1 = echarts.init(document.getElementById(chart));

	let option = {
	    title : {
	        text: title,	        
	        x:'center'
	    },
	    tooltip : {
	        trigger: 'item',
	        formatter: "{b} : {c} ({d}%)"
	    },
	    // legend: {
	    //     orient: 'vertical',
	    //     left: 'left',
	    //     data: []
	    // },
	    series : [
	        {
	            name: title,
	            type: 'pie',
	            radius : '55%',
	            center: ['50%', '60%'],
	            data:data,
	            itemStyle: {
	                emphasis: {
	                    shadowBlur: 10,
	                    shadowOffsetX: 0,
	                    shadowColor: 'rgba(0, 0, 0, 0.5)'
	                }
	            }
	        }
	    ]
	};
	chart1.setOption(option);	
}

</script>
<?php require_once('footer.php')?>
</body>
</html>
