<!DOCTYPE html>
<html lang="en">
    
<head>
    <title>管理员登录登录</title>
	<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="/static/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/static/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="/static/css/matrix-login.css" />
    <link href="/static/font-awesome/css/font-awesome.css" rel="stylesheet" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>

</head>
<body>
    <div id="loginbox">            
        <form id="loginform" class="form-vertical" action="/account/login">
			<div class="control-group normal_text"> <h3>恶臭小程序后台管理</h3></div>
            <div class="control-group">
                <div class="controls">
                    <div class="main_input_box">
                        <span class="add-on bg_lg" id="account_name_span"><i class="icon-user"></i></span><input type="text" placeholder="用户名" id="account_name"/>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <div class="main_input_box">
                        <span class="add-on bg_ly" id="account_pwd_span"><i class="icon-lock"></i></span><input type="password" placeholder="密码" id="account_pwd"/>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <span class="pull-left " id="errorMsg" style="display: none">请输入正确的用户名密码.</span>
                <span class="pull-right" id="btn_login"><a type="submit" href="#" class="btn btn-success"  /> 登录</a></span>
            </div>
        </form>
    </div>
    
    <script src="/static/js/jquery.min.js"></script>  
    <script src="/static/js/matrix.login.js"></script>
    <script type="text/javascript">
        function do_login(){
            var account_name = $('#account_name').val()
            var account_pwd = $('#account_pwd').val()
            if(account_name==''){
                $('#account_name_span').attr('class','add-on bg_lo')
                return 
            }
            if(account_pwd==''){
                $('#account_pwd_span').attr('class','add-on bg_lo')
            }

            $.ajax({
                url:"/api/account/login",
                type:"POST",
                dataType: 'json',
                data:{
                    account_name:account_name,
                    account_pwd:account_pwd
                },
                success:function(res){                        
                    if(res.error<0){
                        $('#errorMsg').html(res.message);
                        $('#errorMsg').show();
                        return 
                    }

                    
                    //window.location.href = window.backurl;
                    var backurl = getQueryString('backurl')
                    if(backurl){
                        window.location.href = backurl;    
                    }else{
                        window.location.href = "/account/index";
                    }
                    

                },
                error:function(){
                  $('#errorMsg').html('请求服务失败，请检查网络');
                  $('#errorMsg').show();
                }
            })
        }
        function getQueryString(name) { 
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
            var r = window.location.search.substr(1).match(reg); 
            if (r != null) return unescape(r[2]); return null; 
        } 
        $(document).ready(function(){
            $("#btn_login").click( function (){
                do_login()
            }),
            $("#account_name").keypress(function (){
                $('#account_name_span').attr('class','add-on bg_lg')
                $('#errorMsg').hide();
            }),
            $("#account_pwd").keypress(function (event){
                $('#account_pwd_span').attr('class','add-on bg_ly')
                $('#errorMsg').hide();
                if(event.which==13){
                    do_login()

                }
            })
        })
        
        
    </script>
</body>

</html>
