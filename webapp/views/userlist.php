<!DOCTYPE html>
<html lang="en">
<head>
<title>恶臭小程序管理后台</title>
<?php require_once('global_css.php')?>
<style type="text/css">
.search{
	position: absolute;
    z-index: 25;
    top: 3px;
    right: 10px;
}
.search button {
    border: 0;
    margin-left: -3px;
    margin-top: -11px;
    padding: 5px 10px 4px;
}
.table-text-center td{
	text-align: center;
}
.pagination ul{
	padding-right: 10px
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
    <div id="breadcrumb"> <a href="/user/userlist" title="用户管理" class="tip-bottom"><i class="icon-home"></i> 用户管理</a></div>
  </div>
<!--End-breadcrumbs-->
	 <div class="container-fluid">


		<div class="widget-box">
          <div class="widget-title">

          	<span class="icon"><input type="checkbox" id="chk_select_all"/></span>
            <h5>用户列表</h5>
          </div>
          
          <div class="widget-content nopadding">
          	<div class="search">
          		<input type="text" placeholder="搜索用户..." id="input_keyword" value="<?=htmlspecialchars($keyword)?>">
          		<button type="submit" class="btn-success" title="Search" id='btn_search'><i class="icon-search icon-white"></i></button>
          	</div>
          	
            <table class="table table-bordered table-striped with-check table-text-center">
              <thead>
                <tr>
                  <th><i class="icon-resize-vertical"></i></th>
                  <th>ID</th>
                  <th>昵称</th>
                  <th>真名</th>
                  <th>电话</th>
                  <th>姓别</th>
                  <th>用户类型</th>
                  <th>状态</th>
                  <th>加入时间</th>
                  <th>屏蔽</th>
                  <th>网格员</th>
                </tr>
              </thead>
              <tbody>
              	<?php
              	foreach ($users as $user) {?>
                <tr >
                  <td><input type="checkbox" value="<?=$user['pk_user']?>"  name="userids[]" id="chk_user_ids"/></td>
                  <td><?php echo $user['pk_user']?></td>
                  <td><?php echo htmlspecialchars($user['nickname'])?></td>
                  <td><?php echo htmlspecialchars($user['realname'])?></td>
                  <td><?php echo htmlspecialchars($user['tel'])?></td>
                  <td><?php
                  	switch ($user['gender']) {
                  		case '1':
                  			echo '女';
                  			break;
                  		case '2':
                  			echo '男';
                  			break;
                  		default:
                  			echo '未知';
                  			break;
                  	}?></td>
                  <td id="table_td_user_type_<?=$user['pk_user']?>"><?php 
                  	switch ($user['user_type']) {
                  		case '1':
                  			echo "网格员";
                  			break;
                  		default:
                  			echo "普通";
                  			break;
                  	}
                  ?></td>
                  <td id="table_td_state_<?=$user['pk_user']?>"><?php 
                  	switch ($user['state']) {
                  		case '1':
                  			echo "屏蔽";
                  			break;
                  		default:
                  			echo "正常";
                  			break;
                  	}
                  ?></td>
                  
                  <td><?php echo $user['createtime']?></td>
                  <td>
                  	<button class="btn btn-mini <?=$user['state']!=1?'btn-danger':'btn-primary'?>" id="btn_close_user" userid="<?=$user['pk_user']?>" state="<?=$user['state']?>"><?=$user['state']!=1?'屏蔽':'解除'?></button>
                  	
                  </td>
                  <td>
                  	
                  	<button class="btn btn-mini <?=$user['user_type']==1?'btn-success':'btn-primary'?>" id="btn_unbind_wgy" userid="<?=$user['pk_user']?>">
                  		<?php echo $user['user_type']==1?'解绑':'绑定'?>
                  	</button>
                  </td>
                </tr>
            	<?php
            	}?>
              </tbody>
            </table>
          </div>

           <div class="pagination text-right">
              <ul>
                <li><a href="?page=<?=$page-1?>&keyword=<?=urldecode($keyword)?>">前一页</a></li>
                <?php 
                for($i=1;$i<=$total_page;$i++){?>
                <li <?=$page==$i?'class="active"':''?> > <a href="?page=<?=$i?>&keyword=<?=urldecode($keyword)?>"><?=$i?></a> </li>
                <?php }?>

                <li><a href="?page=<?=$page+1?>&keyword=<?=urldecode($keyword)?>">后一页</a></li>
              </ul>
          </div>
          
        </div>
    </div>  





</div>
<!--end-main-container-part-->



<script type="text/javascript">
__hook_funs.push(function(){
	//监控事件
	$('#input_keyword').keypress(function (event){
        if(event.which==13){
            doSearch();
        }
    });

    $('#btn_search').click( function (){
        doSearch()
    });

    $('#chk_select_all').click(function(){
    	if( $('#chk_select_all').prop('checked') ){

    		$("input[id='chk_user_ids']").each(function(){
    			$(this).prop('checked',true);
    		})
    	}else{
    		$("input[id='chk_user_ids']").each(function(){
    			$(this).prop('checked',false);
    		})
    	}
    });

    $("button[id='btn_close_user']").each(function(){
    	var that=$(this);

    	$(this).click(function(){
    		var userid = $(this).attr("userid")
    		var state = $(this).attr("state");
    		
    		$.ajax({
    			url:"/api/user/user_state",
    			type:"get",
    			dataType:"json",
    			data:{
    				userid:userid,
    				state:state==0?1:0 
    			},
    			success:function(res){

    				if(res.error<0){
    					alert(res.message);
    					return
    				}

  					if(res.result=="1"){
  						that.attr('state','1')
  						that.prop('class','btn btn-mini btn-primary')
  						that.html('解除')
  						$('#table_td_state_'+userid).html('屏蔽')
  						
  					}else{
  						that.attr('state','0')
  						that.prop('class','btn btn-mini btn-danger')
  						that.html('屏蔽')
  						$('#table_td_state_'+userid).html('正常')
  					}

    			},
    			error:function(){
    				alert("服务器请求出错！");
    			}

    		})
    	})    	
    });

    $("button[id='btn_unbind_wgy']").each(function(){
    	var that = $(this);
    	$(this).click(function(){
    		var userid = $(this).attr("userid")
    		$.ajax({
    			url:"/api/user/user_unbind_wgy",
    			type:"get",
    			dataType:"json",
    			data:{
    				userid:userid
    			},
    			success:function(res){
    				if(res.result=="0"){
    					$('#table_td_user_type_'+userid).html('普通')
    					that.prop('class','btn btn-mini btn-primary')
    					that.html('绑定')
    				}
    				if(res.result=="1"){
    					$('#table_td_user_type_'+userid).html('网格员')
    					that.prop('class','btn btn-mini btn-success')
    					that.html('解绑')
    				}
    			},
    			error:function(){

    			}
    		})
    	})
    });

    $('#input_keyword').focus();

})


	
function doSearch(){
	var keyword = $('#input_keyword').val();
	window.location.href = "?keyword="+encodeURIComponent(keyword)
}
</script>



<?php require_once('footer.php')?>


</body>
</html>
