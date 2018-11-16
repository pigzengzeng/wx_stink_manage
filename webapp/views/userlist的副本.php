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
          <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
            <h5>用户列表</h5>
          </div>
          
          <div class="widget-content ">
          	<div class="search">
          		<input type="text" name="" placeholder="输入用户名...">
          		<button type="submit" class="btn-success" title="Search"><i class="icon-search icon-white"></i></button>
          	</div>
          	
            <table class="table table-bordered data-table with-check table-text-center" data-toggle="table" data-pagination="true">
              <thead>
                <tr>
                  <th><input type="checkbox" id="select_all"/></th>
                  <th>ID</th>
                  <th>昵称</th>
                  <th>真名</th>
                  <th>电话</th>
                  <th>姓别</th>
                  <th>用户类型</th>
                  <th>状态</th>
                  <th>加入时间</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
              	<?php
              	foreach ($users as $user) {?>
                <tr >
                  <td><input type="checkbox" value="<?=$user['pk_user']?>" /></td>
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
                  <td><?php 
                  	switch ($user['user_type']) {
                  		case '1':
                  			echo "网格员";
                  			break;
                  		default:
                  			echo "普通";
                  			break;
                  	}
                  ?></td>
                  <td><?php 
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
                  	<button class="btn btn-mini btn-danger">屏蔽</button>
                  	<button class="btn btn-mini btn-danger">解除网格员</button>
                  </td>
                </tr>
            	<?php
            	}?>
              </tbody>
            </table>
            <div class="pagination">
              <ul>
                <li><a href="?page=<?=$page-1?>">前一页</a></li>
                <?php 
                for($i=1;$i<=$total_page;$i++){?>
                <li <?=$page==$i?'class="active"':''?> > <a href="?page=<?=$i?>"><?=$i?></a> </li>
                <?php }?>

                <li><a href="?page=<?=$page+1?>">后一页</a></li>
              </ul>
          </div>
          </div>
          
        </div>
    </div>  





</div>
<!--end-main-container-part-->

<?php require_once('footer.php')?>


</body>
</html>
