<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:78:"/Applications/MAMP/htdocs/farm/public/../application/home/view/index/main.html";i:1516084571;s:78:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/css.html";i:1516084571;s:77:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/js.html";i:1516084571;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>信息管理平台</title>
        <link href="__PUBLIC__/img/favicon.ico" rel="shortcut icon" >
    <link href="__PUBLIC__/css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
    <link href="__PUBLIC__/css/bootstrap-theme.min.css?v=3.3.5" rel="stylesheet">
    <link href="__PUBLIC__/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="__PUBLIC__/css/animate.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/plugins/bootstraptable/bootstrap-table.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/plugins/bootstrapselect/bootstrap-select.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/plugins/datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/plugins/info/information.css" rel="stylesheet">
    <style type="text/css">
        html{background-color: #f5f5f5}
        .table thead {background-color: #ccc;}


    </style>
    <link href="__PUBLIC__/css/style.min862f.css?v=4.1.0" rel="stylesheet">
</head>
<body>
    <div class="panel well">

     <section class="content">
		<div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="widget yellow-bg p-lg text-center">
                  <div class="m-b-md">
                      <i class="fa fa-bell fa-4x"></i>
                      <h1 class="m-xs"><?php echo $count['handle_order']; ?></h1>
                      <h3 class="font-bold no-margins">
                              待处理订单
                          </h3>
                      <!-- <small>账户</small> -->
                  </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="widget lazur-bg p-lg text-center">
                  <div class="m-b-md">
                      <i class="fa fa-book fa-4x"></i>
                      <h1 class="m-xs"><?php echo $count['goods']; ?></h1>
                      <h3 class="font-bold no-margins">
                              商品数量
                          </h3>
                      <!-- <small>账户</small> -->
                  </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="widget navy-bg p-lg text-center">
                  <div class="m-b-md">
                      <i class="fa fa-files-o fa-4x"></i>
                      <h1 class="m-xs"><?php echo $count['article']; ?></h1>
                      <h3 class="font-bold no-margins">
                              文章数量
                          </h3>
                      <!-- <small>账户</small> -->
                  </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="widget red-bg p-lg text-center">
                  <div class="m-b-md">
                      <i class="fa fa-users fa-4x"></i>
                      <h1 class="m-xs"><?php echo $count['users']; ?></h1>
                      <h3 class="font-bold no-margins">
                              会员总数
                          </h3>
                      <!-- <small>账户</small> -->
                  </div>
              </div>
            </div>

         </div>
		
		<div class="row">
			<div class="col-md-12">
		      <div class="panel panel-primary">
		        <div class="panel-heading">
		          今日统计
		        </div>
		        <div class="panel-body">
	         		<div class="row">
			  			<div class="col-sm-3 col-xs-6">
			  				新增订单：<?php echo $count['new_order']; ?>
			  			</div>
			  				<div class="col-sm-3 col-xs-6">
			  				今日访问：<?php echo $count['today_login']; ?>
			  			</div>
			  				<div class="col-sm-3 col-xs-6">
			  				新增会员：<?php echo $count['new_users']; ?>
			  			</div>
			  				<div class="col-sm-3 col-xs-6">
			  				待审评论：<?php echo $count['comment']; ?>
			  			</div>
		  			</div>
		        </div>
		      </div>
		    </div>
		</div>

     </section>
 </div>
 	<script type="text/javascript">
		var publicUrl = '__PUBLIC__';
		var imgUrl    = '__PUBLIC__/img/';
		var jsUrl     = '__PUBLIC__/js/';
	</script>
	<script type="text/javascript" src="__PUBLIC__/js/jquery.min.js?v=1.9"></script>
	<script type="text/javascript" src="__PUBLIC__/js/bootstrap.min.js?v=3.3.5"></script>
	<script type="text/javascript" src="__PUBLIC__/js/information.js"></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/layer/layer.js?v=2.2"></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/bootstraptable/bootstrap-table.min.js" ></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/bootstraptable/bootstrap-table-zh-CN.js" ></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/bootstrapselect/bootstrap-select.min.js" ></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/datetimepicker/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/datetimepicker/bootstrap-datetimepicker.zh-CN.js"></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/upload/jquery.uploadify.min.js" ></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/chosen/chosen.jquery.min.js" ></script>
	<script type="text/javascript" src="__PUBLIC__/js/myFormValidate.js"></script>
 </body>
 </html>