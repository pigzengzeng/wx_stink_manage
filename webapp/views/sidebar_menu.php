
<!--sidebar-menu-->
<div id="sidebar"><a href="#" class="visible-phone"><i class="icon icon-home"></i> 控制台</a>
  <ul>
    <li <?php if($current_url=='/account/index') echo 'class="active"';?>>
    	<a href="/account/index"><i class="icon icon-home"></i><span>首页</span></a>
    </li>
    <li <?php if($current_url=='/user/userlist') echo 'class="active"';?>>
    	<a href="/user/userlist"><i class="icon icon-user"></i><span>用户管理</span></a>
    </li>

    <li <?php if($current_url=='/marker/marker_map') echo 'class="active"';?>>
    	<a href="/marker/marker_map"><i class="icon icon-pushpin"></i><span>标记管理</span></a>
    </li>


    
  </ul>
</div>
<!--sidebar-menu-->