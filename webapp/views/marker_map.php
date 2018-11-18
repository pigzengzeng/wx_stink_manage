<!DOCTYPE html>
<html lang="en">
<head>
<title>恶臭小程序管理后台</title>
<?php require_once('global_css.php')?>

<!--这是移动端用的，可以使地图显示的效果更佳-->
<meta name="viewport" content="initial-scale=1.0, user-scalable=no"> 


<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.10&key=ddafe9c7c9f13d041501859abbacc05d"></script> 
<style type="text/css">
	
	
	#map_container {
		height:500px;
	}
	.view_control{
		padding: 6px;
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
    <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i>标记管理</a></div>
  </div>
<!--End-breadcrumbs-->
  <div class="container-fluid">
  	<div class="row-fluid">
	  	<div id="map_container" class="span10"></div>
	  	<div id="marker_photo" class="span2">
	  		<div class="widget-box">
	          <div class="widget-title"> <span class="icon"> <i class="icon-picture"></i> </span>
	            <h5>现场照片</h5>
	          </div>
	          <div class="widget-content">
	            <ul class="thumbnails" id="ul_photo_list">
	              
	            </ul>
	          </div>
	        </div>
	  	</div>
	  	<div class="span10">
	  		<div class="view_control span12">
	  				
			  		<button class="btn btn-primary" id="btn_wgy_marker">网格员标记</button>	
			  		<button class="btn btn-primary" id="btn_user_marker">用户标记</button>
			  		<button class="btn btn-primary" id="btn_wgy_position">网格员位置</button>
			  		<button class="btn btn-primary" id="btn_time_today">本日</button>
			  		<button class="btn" id="btn_time_week">本周</button>
			  		<button class="btn" id="btn_time_month">本月</button>

		  			
		  			
		  			<!--
		  			<div class="span7">
		  				<lable class="control-label">从</lable>
				  		<div class="input-append date" id="datetimepicker" data-date="12-02-2012" data-date-format="dd-mm-yyyy">
						    <input  size="16" type="text" value="12-02-2012">
						    <span class="add-on"><i class="icon-th"></i></span>
						</div>

						<lable class="control-label">到</lable>
						<div class="input-append date" id="datetimepicker" data-date="12-02-2012" data-date-format="dd-mm-yyyy">
						    <input size="16" type="text" value="12-02-2012">
						    <span class="add-on"><i class="icon-th"></i></span>
						</div>       
		            </div>
		        	-->
		        
	  		</div>
	  	</div>
	</div>
  </div>
</div>
<!--end-main-container-part-->

<script type="text/javascript">
	var res_request_url = '<?=$res_request_url?>';

	var searchFilter = {
		wgyMarker:true,
		userMarker:true,
		wgyLastPosition:true,
		timeFlag:'today'
	}
	var markerList = []; //地图上的marker点
	var userList = []; //地图上最近活动的用户点

	
	

	__hook_funs.push(function(){

		var map = new AMap.Map('map_container',{
			zoom:10
		});


		map.on('moveend',function(e){
			refreshMarkers()
		})

		

		$(function(){
			refreshMarkers();
			refreshUsers();
		})
		

		$(function(){
			$("#btn_wgy_marker").click(function(){
				searchFilter.wgyMarker = !searchFilter.wgyMarker
				if(searchFilter.wgyMarker){
					$("#btn_wgy_marker").prop('class','btn btn-primary')
				}else{
					$("#btn_wgy_marker").prop('class','btn')
				}
				refreshMarkers()
			})
			$("#btn_user_marker").click(function(){
				searchFilter.userMarker = !searchFilter.userMarker
				if(searchFilter.userMarker){
					$("#btn_user_marker").prop('class','btn btn-primary')
				}else{
					$("#btn_user_marker").prop('class','btn')
				}
				refreshMarkers()
			})

			$("#btn_wgy_position").click(function(){
				searchFilter.wgyLastPosition = !searchFilter.wgyLastPosition
				if(searchFilter.wgyLastPosition){
					$("#btn_wgy_position").prop('class','btn btn-primary')
				}else{
					$("#btn_wgy_position").prop('class','btn')
				}
				refreshUsers()

			})

			$("#btn_time_today").click(function(){
				searchFilter.timeFlag = 'today'
				$("#btn_time_today").prop('class','btn btn-primary')
				$("#btn_time_week").prop('class','btn')
				$("#btn_time_month").prop('class','btn')
				refreshMarkers()
			})
			$("#btn_time_week").click(function(){
				searchFilter.timeFlag = 'week'
				$("#btn_time_today").prop('class','btn')
				$("#btn_time_week").prop('class','btn btn-primary')
				$("#btn_time_month").prop('class','btn')
				refreshMarkers()
			})
			$("#btn_time_month").click(function(){
				searchFilter.timeFlag = 'month'
				$("#btn_time_today").prop('class','btn')
				$("#btn_time_week").prop('class','btn')
				$("#btn_time_month").prop('class','btn btn-primary')
				refreshMarkers()
			})


		})




		function refreshUsers(){
			if(!searchFilter.wgyLastPosition){
				map.remove(userList)
				userList = []
				return 
			}

			$.ajax({
				url:"/api/user/get_user_last_position",
				type:"get",
				dataType:"json",
				success:function(res){
					map.remove(userList);
					userList = []
					if(!res.result){
						return 
					}

					var users = res.result;
					
					
					for(var i=0;i<users.length;i++){
						
						var userId = users[i].pk_user
						
						//var user_icon = "/static/map_imgs/user_face_"+(userId%10)+".svg";
						var user_icon = "/static/map_imgs/wgy.png";
						var content = "<img src='"+user_icon+"' width='20' height='32'/>"
						var last_lat = users[i].last_lat;
						var last_lon = users[i].last_lon;

						if(last_lat==0 || last_lon==0)continue;

						var user = new AMap.Marker({
							content :content,
							offset:  new AMap.Pixel(-16, -44),
							position:new AMap.LngLat(last_lon,last_lat),
							extData:users[i]
						});

						user.on('click',openUserInfo)

						

						userList.push(user);
						
					    
					}

					map.add(userList)
				}
			});


		}

		function refreshMarkers(){
			var markerLevel = []
			if(searchFilter.userMarker){
				markerLevel.push(0)
			}
			if(searchFilter.wgyMarker){
				markerLevel.push(1)
			}

			var timestamp=new Date().getTime()
			timestamp = timestamp/1000

			switch(searchFilter.timeFlag){

				case 'week':
					var timeFrom = timestamp-86400*7;
					var timeTo = timestamp;
					break
				case 'month':
					var timeFrom = timestamp-86400*30;
					var timeTo = timestamp;
					break
				default:
					var timeFrom = timestamp-86400;
					var timeTo = timestamp;

			}

			var bounds = map.getBounds()

			var northEast = bounds.getNorthEast()
			var southWest = bounds.getSouthWest()
			
			var x1 = northEast.getLng()
			var y1 = northEast.getLat()
			var x2 = southWest.getLng()
			var y2 = southWest.getLat()

            




			$.ajax({
				url:"/api/marker/get_markers",
				type:"get",
				dataType:"json",
				data:{
					marker_level:markerLevel.join(','),
					time_from:timeFrom,
					time_to:timeTo,
					x1:x1,
					y1:y1,
					x2:x2,
					y2:y2
				},
				success:function(res){
					map.remove(markerList);
					markerList = []
					if(!res.result.markers){
						return 
					}

					

					var markers = res.result.markers;
					
					
					for(var i=0;i<markers.length;i++){
						var odour = markers[i].odour
						var intensity = markers[i].intensity
						var marker_icon = "/static/map_imgs/marker_"+odour+"_"+intensity+".png";
						var content = "<img src='"+marker_icon+"' width='50' height='44'/>"
						var markerId = markers[i].id;


						var marker = new AMap.Marker({
							content :content,
							offset:  new AMap.Pixel(-16, -44),
							position:new AMap.LngLat(markers[i].longitude,markers[i].latitude),
							extData:{markerId:markerId}
						});

						marker.on('click',openMarkerInfo)
						

						markerList.push(marker);


						
						//鼠标点击marker弹出自定义的信息窗体
					    // AMap.event.addListener(marker, 'click', openInfo);
					    
					}
					map.add(markerList)
				}
			});

		}

		


		
		function openUserInfo(e){
			var user = e.target.getExtData()
			var position = e.target.getPosition()
			var content = [
				    "用户:"+(user.nickname?user.nickname:'匿名用户'),
				    "真名:"+(user.realname?user.realname:''),
				    "最后所在位置:"+formatDegree(user.last_lon) +","+formatDegree(user.last_lat),
				    "最后更新时间:"+user.lastupdate
				];
			var infoWindow = new AMap.InfoWindow({
			   content: content.join("<br>"),
			   offset:new AMap.Pixel(14,-43)
			});
			infoWindow.open(map, position);

		}
		function openMarkerInfo(e){
			var markerId = e.target.getExtData().markerId
			var position = e.target.getPosition()
			$.ajax({
				url:"/api/marker/get_marker?markerid="+markerId,
				type:"get",
				dataType:"json",
				success:function(res){
					var content = [
					    "markerId:"+res.result.markerId,
					    "用户:"+(res.result.user.nickname?res.result.user.nickname:'匿名用户'),
					    "真名:"+(res.result.user.realname?res.result.user.realname:''),
					    "位置:"+formatDegree(res.result.longitude) +","+formatDegree(res.result.latitude),
					    "提交时间:"+res.result.createtime,
					    "用户类型:"+(res.result.user.user_type==1?'网格员':'一般用户')
					    
					];
					var infoWindow = new AMap.InfoWindow({
					   content: content.join("<br>"),
					   offset:new AMap.Pixel(14,-43)
					});
					infoWindow.open(map, position);

					//显示图片
					$('#ul_photo_list').empty();
					var photo_files = [];

					if(res.result.photo_files){
						photo_files = res.result.photo_files
					}

					for(var i=0;i<photo_files.length;i++){

						var content = [
							'<li class="span12" >',
			              		'<a href="#"> <img src="'+res_request_url+photo_files[i]+'" alt="" onclick="preview_photo(this)" > </a>',
		              		'</li>'
						];
						$('#ul_photo_list').append(content.join("\n"));

					}
					

				}
			})
		}
		
		$(function () {
        	$(".date").datepicker({
	            language: "zh-CN",
	            autoclose: true,//选中之后自动隐藏日期选择框
	            clearBtn: true,//清除按钮
	            todayBtn: true,//今日按钮
	            pickerPosition: "bottom-left",
	            format: "yyyy-mm-dd"//日期格式，详见 http://bootstrap-datepicker.readthedocs.org/en/release/options.html#format
        	});
    	});

		$('#map_container').css('height',$(document).height()*0.6)
	});

	function preview_photo(e) {
		
		
		
		var image_href = $(e).attr("src");
		
		if ($('#lightbox').length > 0) {
			$('#imgbox').html('<img src="' + image_href + '" /><p><i class="icon-remove icon-white"></i></p>');
			$('#lightbox').slideDown(500);
		}
		else { 
			var lightbox = 
			'<div id="lightbox" style="display:none;">' +
				'<div id="imgbox"><img src="' + image_href +'"/>' + 
					'<p><i class="icon-remove icon-white"></i></p>' +
				'</div>' +	
			'</div>';
				
			$('body').append(lightbox);
			$('#lightbox').slideDown(500);
		}
		
		var img = $('#imgbox').find('img');
		if(img){
			var maxHeight = Math.ceil($(window).height()*0.7)
			var maxWidth =  Math.ceil($(window).width()*0.6)
			
			if( $(img).prop('height')>$(img).prop('width') ){ //竖图，限定最大高
				if( $(img).prop('height') > maxHeight ){

					$(img).prop('height',maxHeight)
					$(img).prop('width',
						$(img).prop('width') *(maxHeight / $(img).prop('height') ) 
					)
				}
			}else{ //横图限定最大宽
				if( $(img).prop('width') > maxWidth ){
					$(img).prop('width',maxWidth)

					$(img).prop('height',
						$(img).prop('height') *(maxWidth / $(img).prop('width') ) 
					)

				}
			}
			
		}
	
		


		$('#lightbox').live('click', function() { 
			$('#lightbox').hide(200);
		});




	};


function formatDegree(value) {
  ///<summary>将度转换成为度分秒</summary>
  value = Math.abs(value);
  var v1 = Math.floor(value);//度
  var v2 = Math.floor((value - v1) * 60);//分
  var v3 = Math.round((value - v1) * 3600 % 60);//秒
  return v1 + '°' + v2 + '\'' + v3 + '"';
}

</script>



<?php require_once('footer.php')?>

</body>
</html>
