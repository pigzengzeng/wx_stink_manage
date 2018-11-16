<!--Header-part-->
<div id="header">
  <h1><a href="#">格新环境</a></h1>
</div>
<!--close-Header-part-->
<!--top-Header-menu-->
<div id="user-nav" class="navbar navbar-inverse">
  <ul class="nav">
    <li  class="dropdown" id="profile-messages" ><a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle"><i class="icon icon-user"></i>  <span class="text" id="top_span_account_name">欢迎</span><b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a href="/account/change_pwd"><i class="icon-user"></i> 我的资料</a></li>        
        <li class="divider"></li>
        <li><a href="/account/logout"><i class="icon-key"></i> 退出</a></li>
      </ul>
    </li>
    <li class=""><a title="" href="/account/change_pwd"><i class="icon icon-cog"></i> <span class="text">设置</span></a></li>
    <li class=""><a title="" href="/account/logout"><i class="icon icon-share-alt"></i> <span class="text">退出</span></a></li>
  </ul>
</div>
<script type="text/javascript">
  
  __hook_funs.push(function(){
    $.ajax({
      url:"/api/account/get_account",
      type:"get",
      dataType:"json",
      success:function(res){
        if(res.error<0)return 
        $('#top_span_account_name').html('欢迎'+res.result.account_name);
        
      }
    })
  })
  
</script>
<!--close-top-Header-menu-->

<!--start-top-serch
<div id="search">
  <input type="text" placeholder="输入搜索内容..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div>
close-top-serch-->