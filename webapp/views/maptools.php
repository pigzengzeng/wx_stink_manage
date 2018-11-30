<!DOCTYPE html>
<html lang="en">
<head>
<title>恶臭小程序管理后台</title>

<meta name="viewport" content="initial-scale=1.0, user-scalable=no"> 
<link rel="stylesheet" href="/static/css/bootstrap.min.css" />

<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.10&key=ddafe9c7c9f13d041501859abbacc05d"></script> 
<script src="//webapi.amap.com/ui/1.0/main.js"></script>

<script src="/static/js/jquery.min.js"></script> 
<script src="/static/js/bootstrap.min.js"></script> 
<style type="text/css">
	
	
	#map_container {
		height:400px;
	}

</style>

</head>

<body>
	<div id="map_container" class="span6">
		
	</div>

	<div id="content" class="span6">
		
		    <input type="text" placeholder="输入adcode" id="input_adcode" value="110000" />
		    <button type="button" id="btn_search">查寻</button>



		    <div id="div_info"></div>
        	<label>边界</label>
			<div id="div_districtCoordinates"></div>
        </div>

		
	</div>

</body>

<script type="text/javascript">
	

	$(document).ready(function(){
		var map = new AMap.Map('map_container',{
			zoom:10
		});
		$("#btn_search").click(function(){
			var adcode = $("#input_adcode").val()
			if(adcode!=''){
				searchCity(adcode)
			}
		})

		function searchCity(adcode){
			/*
			map.plugin('AMap.CitySearch', function () {
	    		var city = new AMap.CitySearch()
	    		city.getLocalCity(function(status,result){
	    			console.log(result)
	    			var content = [
	    				"当前城市："+result.city,
	    				"中心坐标："+result.bounds.getCenter(),
	    				"southWest："+result.bounds.getSouthWest(),
	    				"northEast："+result.bounds.getNorthEast()
	    			]
	    			$('#districtName').html(content.join("</br>"))

	    		})
	    	})*/
	    	//加载DistrictExplorer，loadUI的路径参数为模块名中 'ui/' 之后的部分 
			AMapUI.loadUI(['geo/DistrictExplorer'], function(DistrictExplorer) {		   
			   var districtExplorer = new DistrictExplorer({
			   	map:map
			   })
			   districtExplorer.loadAreaNode(adcode,function(error,areaNode){
			   		if(error){
			   			console.log(error);
			   			return
			   		}
			   		var bounds = areaNode.getBounds()

			   		var feature = areaNode.getParentFeature()
			   		var strFeature=''
			   		var maxLon=0
			   		var maxLat=0
			   		var minLon=180
			   		var minLat=180

			   		//JSON.stringify(feature.geometry.coordinates,null,2)
			   		for(var i = 0 ;i<feature.geometry.coordinates[0].length;i++){
			   			if(i>0)strFeature+=","
			   			strFeature += "[\n"
			   			for(var j=0 ;j<feature.geometry.coordinates[0][i].length;j++){
			   				var lon = feature.geometry.coordinates[0][i][j][0]
			   				var lat = feature.geometry.coordinates[0][i][j][1]
			   				if(lon>maxLon)maxLon=lon
			   				if(lon<minLon)minLon=lon
			   				if(lat>maxLat)maxLat=lat
			   				if(lat<minLat)minLat=lat

			   				strFeature+= "    ["+lon+","+lat+"], \n"
			   			}
			   			strFeature+="]"

			   		}

			   		var content = [
			   			"adcode:"+ areaNode.getAdcode(),
			   			"地区:"+ areaNode.getName(),
			   			"中心位置:"+ bounds.getCenter(),
			   			"东北:"+ bounds.getNorthEast(),
			   			"西南:"+ bounds.getSouthWest(),
			   			"最东北:"+ maxLon+","+maxLat,
			   			"最西南:"+ minLon+","+minLat

			   		]
			   		$("#div_info").html(content.join("</br>"))



			   		var content = "<pre>" + strFeature + "</pre>"
			   		$("#div_districtCoordinates").html(content)


			   		
			   })
			});

		}
		


	})
</script>
</html>