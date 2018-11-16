<!DOCTYPE html>
<html lang="en">
<head>
<title>恶臭小程序管理后台</title>
<?php require_once('global_css.php')?>
</head>
<body>

<?php require_once('top_header.php')?>
<?php require_once('sidebar_menu.php')?>

<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="/account/change_password" title="修改密码" class="tip-bottom"><i class="icon-key"></i> 修改密码</a></div>
  </div>
<!--End-breadcrumbs-->

  <div class="container-fluid">

  	<div class="row-fluid">
	    <div class="span6">
	      <div class="widget-box">
	        <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
	          <h5>修改密码</h5>
	        </div>
	        <div class="widget-content" style="display: none;" id="errorMsg">
		      <div class="alert alert-success">
              	<button class="close" data-dismiss="alert">×</button>
              	<strong>Success!</strong><font></font></div>
	        </div>


	        <div class="widget-content nopadding">

	          <form action="#" method="post" class="form-horizontal" id="form_save">
	            
	            <div class="control-group">
	              <label class="control-label">当前密码</label>
	              <div class="controls">
	                <input type="password"  class="span11" placeholder="Enter Password"  id="input_pwd"/>
	                <span class="help-inline" style="display: none;"></span>
	              </div>
	            </div>

	            <div class="control-group">
	              <label class="control-label">新密码</label>
	              <div class="controls">
	                <input type="password"  class="span11" placeholder="Enter Password"  id="input_new_pwd"/>
	                <span class="help-inline" style="display: none"></span>
	              </div>
	            </div>
	            <div class="control-group">
	              <label class="control-label">再输一次</label>
	              <div class="controls">
	                <input type="password"  class="span11" placeholder="Enter Password"  id="input_repeat_pwd"/>
	                <span class="help-inline" style="display: none"></span>
	              </div>
	            </div>



	            
	            <div class="form-actions">
	              <button type="submit" class="btn btn-success" id="btn_save">保存</button>
	            </div>
	          </form>
	        </div>
	      </div>
	    </div>
	</div>


  </div>


</div>
<!--end-main-container-part-->

<script type="text/javascript">
	__hook_funs.push(function(){
		$("#form_save").submit(function(e){
			e.preventDefault();

			var pwd=$("#input_pwd").val()
			var newPwd=$("#input_new_pwd").val()
			var rePwd=$("#input_repeat_pwd").val()
			if(pwd==''){
				var div_pwd = $("#input_pwd").parent().parent()
				$(div_pwd).prop("class","control-group error")
				$(div_pwd).find("label").prop("for","input_pwd")
				$(div_pwd).find("span").html('请输入当前密码')
				$(div_pwd).find("span").show()
				return false
			}
			if(newPwd==''){
				var div_new_pwd = $("#input_new_pwd").parent().parent()
				$(div_new_pwd).prop("class","control-group error")
				$(div_new_pwd).find("label").prop("for","input_new_pwd")
				$(div_new_pwd).find("span").html('请输入新密码')
				$(div_new_pwd).find("span").show()
				return false
			}
			if(rePwd==''){
				var div_new_pwd = $("#input_repeat_pwd").parent().parent()
				$(div_new_pwd).prop("class","control-group error")
				$(div_new_pwd).find("label").prop("for","input_repeat_pwd")
				$(div_new_pwd).find("span").html('请再输一次新密码')
				$(div_new_pwd).find("span").show()
				return false
			}
			if(rePwd!=newPwd){
				var div_new_pwd = $("#input_repeat_pwd").parent().parent()
				$(div_new_pwd).prop("class","control-group error")
				$(div_new_pwd).find("label").prop("for","input_repeat_pwd")
				$(div_new_pwd).find("span").html('两次密码不一致')
				$(div_new_pwd).find("span").show()
				return false
			}

			$.ajax({
                url:"/api/account/change_pwd",
                type:"POST",
                dataType: 'json',
                data:{
                    pwd:pwd,
                    new_pwd:newPwd
                },
                success:function(res){                        
                    if(res.error<0){
                        $('#errorMsg').find("div").prop("class","alert alert-error")
                        $('#errorMsg').find("font").text(res.message);
                        $('#errorMsg').find("strong").text("Error!");
                        $('#errorMsg').show();
                        return 
                    }

                    $('#errorMsg').find("div").prop("class","alert alert-success")
                    $('#errorMsg').find("font").text("密码修改完成");
                    $('#errorMsg').find("strong").text("Success!");
                    $('#errorMsg').show();


                },
                error:function(){
                  $('#errorMsg').find("div").prop("class","alert alert-error")
                  $('#errorMsg').find("font").html("服务器请求出错！");
                  $('#errorMsg').find("strong").text("Error!");
                  $('#errorMsg').show();
                }
            })




		})
		$("#input_pwd").keypress(function(e){
			var div_pwd = $("#input_pwd").parent().parent()
			$(div_pwd).prop("class","control-group")
			$(div_pwd).find("label").prop("for","input_pwd")
			$(div_pwd).find("span").hide()
		})
		$("#input_new_pwd").keypress(function(e){
			var div_pwd = $("#input_new_pwd").parent().parent()
			$(div_pwd).prop("class","control-group")
			$(div_pwd).find("label").prop("for","input_new_pwd")
			$(div_pwd).find("span").hide()
		})
		$("#input_repeat_pwd").keypress(function(e){
			var div_pwd = $("#input_repeat_pwd").parent().parent()
			$(div_pwd).prop("class","control-group")
			$(div_pwd).find("label").prop("for","input_repeat_pwd")
			$(div_pwd).find("span").hide()
		})



	})
</script>
<?php require_once('footer.php')?>
</body>
</html>
