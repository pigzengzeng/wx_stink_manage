<!DOCTYPE html>
<html lang="en">
<head>
<title>恶臭小程序管理后台</title>
<?php require_once('global_css.php')?>

<!--这是移动端用的，可以使地图显示的效果更佳-->
<meta name="viewport" content="initial-scale=1.0, user-scalable=no"> 
</head>
<body>

<?php require_once('top_header.php')?>
<?php require_once('sidebar_menu.php')?>

<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="/conf/message" title="Go to Home" class="tip-bottom"><i class="icon-home"></i>短信管理</a></div>
  </div>
<!--End-breadcrumbs-->
  <div class="container-fluid">
  	<div class="row-fluid">
	  	<div class="span8">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
          <h5>接收短信条件设置</h5>
        </div>
        <div class="widget-content nopadding">
          <form action="#" method="get" class="form-horizontal">
            <div class="control-group">
              <label class="control-label">气味强度</label>
              <div class="controls">
                <label>
                  <input type="radio" name="intensity" value="1" /> 轻微及以上
                </label>
                <label>
                  <input type="radio" name="intensity" value="2"/> 一般及以上
                </label>
                <label>
                  <input type="radio" name="intensity" value="3"/> 强烈及以上
                </label>
                <label>
                  <input type="radio" name="intensity" value="4"/> 难忍及以上
                </label>                
              </div>
            </div>

            <div class="control-group">
              <label class="control-label">手机号:</label>
              <div class="controls">
                <textarea class="span11" name="tel" rows="4"></textarea>
                <span class="help-block">多个手机号之间用半角逗号分隔</span>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label">AccessKeyId:</label>
              <div class="controls">
                <input type="text" name="access_key_id" class="span8">                
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">AccessKeySecret:</label>
              <div class="controls">
                <input type="text" name="access_key_secret" class="span8">                
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">签名名称:</label>
              <div class="controls">
                <input type="text" name="sign_name">                
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">模版CODE:</label>
              <div class="controls">
                <input type="text" name="template_code">                
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-success">保存</button>
            </div>
          </form>
        </div>
      </div>
	</div>
  </div>
</div>
<!--end-main-container-part-->

<script type="text/javascript">
	
	__hook_funs.push(function(){
        $.ajax({
            url:"/api/conf/get_conf_message",
            type:"GET",
            dataType: 'json',            
            success:function(res){                                            
                if(!res.result)return
                let intensity = res.result.intensity
                let tel = res.result.tel
                let access_key_id = res.result.access_key_id
                let access_key_secret = res.result.access_key_secret
                let sign_name = res.result.sign_name
                let template_code = res.result.template_code

                $("input[name='intensity'][value='"+intensity+"']").attr('checked','true')
                $("textarea[name='tel']").val(tel)
                $("input[name='access_key_id']").val(access_key_id)
                $("input[name='access_key_secret']").val(access_key_secret)
                $("input[name='sign_name']").val(sign_name)
                $("input[name='template_code']").val(template_code)

            },
            error:function(){                  
            }
        })   

		$("form").submit(function(e){            
            let intensity = $("input[name='intensity']:checked").val()
            let tel = $("textarea[name='tel']").val()
            let access_key_id = $("input[name='access_key_id']").val()            
            let access_key_secret = $("input[name='access_key_secret']").val()
            let sign_name = $("input[name='sign_name']").val()
            let template_code = $("input[name='template_code']").val()


            if(!intensity){
                intensity=0
            }
            $.ajax({
                url:"/api/conf/save_conf_message",
                type:"POST",
                dataType: 'json',
                data:{
                    intensity:intensity,
                    tel:tel,
                    access_key_id:access_key_id,
                    access_key_secret:access_key_secret,
                    sign_name:sign_name,
                    template_code:template_code
                },
                success:function(res){                                            
                },
                error:function(){                  
                }
            })            
            return false
        })
	});


</script>



<?php require_once('footer.php')?>

</body>
</html>
