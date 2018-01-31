<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/users/index.html";i:1516084569;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cmn-Hans">

	<head>
		<!-- 声明文档使用的字符编码 -->
		<meta charset='utf-8'>
		<!-- 优先使用 IE 最新版本和 Chrome -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<!-- 页面描述 -->
		<meta name="description" content="不超过150个字符" />
		<!-- 页面关键词 -->
		<meta name="keywords" content="" />
		<!-- 网页作者 -->
		<meta name="author" content="guo,652730268@qq.com" />
		<!-- 搜索引擎抓取 -->
		<meta name="robots" content="index,follow" />
		<!-- 为移动设备添加 viewport -->
		<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=3,minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui" />
		<!-- `width=device-width` 会导致 iPhone 5 添加到主屏后以 WebApp 全屏模式打开页面时出现黑边 http://bigc.at/ios-webapp-viewport-meta.orz -->

		<!-- iOS 设备 begin -->
		<meta name="apple-mobile-web-app-title" content="标题">
		<!-- 添加到主屏后的标题（iOS 6 新增） -->
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<!-- 是否启用 WebApp 全屏模式，删除苹果默认的工具栏和菜单栏 -->

		<!--meta name="apple-itunes-app" content="app-id=myAppStoreID, affiliate-data=myAffiliateData, app-argument=myURL" -->
		<!-- 添加智能 App 广告条 Smart App Banner（iOS 6+ Safari） -->
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<!-- 设置苹果工具栏颜色 -->
		<meta name="format-detection" content="telphone=no, email=no" />
		<!-- 忽略页面中的数字识别为电话，忽略email识别 -->

		<!-- 启用360浏览器的极速模式(webkit) -->
		<meta name="renderer" content="webkit">
		<!-- 避免IE使用兼容模式 -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<!-- 不让百度转码 -->
		<meta http-equiv="Cache-Control" content="no-siteapp" />
		<!-- 针对手持设备优化，主要是针对一些老的不识别viewport的浏览器，比如黑莓 -->
		<meta name="HandheldFriendly" content="true">
		<!-- 微软的老式浏览器 -->
		<meta name="MobileOptimized" content="320">
		<!-- uc强制竖屏 -->
		<meta name="screen-orientation" content="portrait">
		<!-- QQ强制竖屏 -->
		<meta name="x5-orientation" content="portrait">
		<!-- UC强制全屏 -->
		<meta name="full-screen" content="yes">
		<!-- QQ强制全屏 -->
		<meta name="x5-fullscreen" content="true">
		<!-- UC应用模式 -->
		<meta name="browsermode" content="application">
		<!-- QQ应用模式 -->
		<meta name="x5-page-mode" content="app">
		<!-- windows phone 点击无高光 -->
		<meta name="msapplication-tap-highlight" content="no">
		<!-- iOS 图标 begin -->
		<link rel="apple-touch-icon-precomposed" href="__PUBLIC__/skin/apple-touch-icon-57x57-precomposed.png" />
		<!-- iPhone 和 iTouch，默认 57x57 像素，必须有 -->
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="__PUBLIC__/skin/apple-touch-icon-114x114-precomposed.png" />
		<!-- Retina iPhone 和 Retina iTouch，114x114 像素，可以没有，但推荐有 -->
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="__PUBLIC__/skin/apple-touch-icon-144x144-precomposed.png" />
		<!-- Retina iPad，144x144 像素，可以没有，但推荐有 -->
		<!-- iOS 图标 end -->

		<!-- iOS 启动画面 begin -->
		<link rel="apple-touch-startup-image" sizes="768x1004" href="__PUBLIC__/skin/img/App-ios-logo-152x152.png" />

		<link rel="apple-touch-startup-image" href="__PUBLIC__/skin/img/App-ios-logo-152x152.png" />
		<!-- iPhone/iPod Touch 竖屏 320x480 (标准分辨率) -->
		<link rel="apple-touch-startup-image" sizes="640x960" href="__PUBLIC__/skin/img/App-ios-logo-152x152.png" />
		<!-- iPhone/iPod Touch 竖屏 640x960 (Retina) -->
		<link rel="apple-touch-startup-image" sizes="640x1136" href="__PUBLIC__/skin/img/App-ios-logo-152x152.png" />
		<!-- iPhone 5/iPod Touch 5 竖屏 640x1136 (Retina) -->
		<!-- iOS 启动画面 end -->

		<!-- iOS 设备 end -->
		<meta name="msapplication-TileColor" content="#000" />
		<!-- Windows 8 磁贴颜色 -->
		<meta name="msapplication-TileImage" content="icon.png" />
		<!-- Windows 8 磁贴图标 -->

		<link rel="alternate" type="application/rss+xml" title="RSS" href="__PUBLIC__/skin/rss.xml" />
		<!-- 添加 RSS 订阅 -->
		<link rel="shortcut icon" type="image/ico" href=__PUBLIC__/skin/favicon.ico" />
		<!-- 添加 favicon icon -->
		<!-- sns 社交标签 begin -->
		<!-- 参考微博API -->
		<meta property="og:type" content="类型" />
		<meta property="og:url" content="URL地址" />
		<meta property="og:title" content="标题" />
		<meta property="og:image" content="图片" />
		<meta property="og:description" content="描述" />
		<title>我的</title>
		
		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">


		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>
        
        <div class="my-gray">

	        <div class="my-box">
	        	<div class="my-box-info">
	        		<div class="my-box-list">
	        			<div class="my-item">
	        				<div class="my-head">
	        					<div class="item-img">
	        						<!-- <img src="__PUBLIC__/skin/img/icon/head-3.jpg" /> -->
	        						<?php if(!(empty($user['head_pic']) || (($user['head_pic'] instanceof \think\Collection || $user['head_pic'] instanceof \think\Paginator ) && $user['head_pic']->isEmpty()))): ?><img src="<?php echo $user['head_pic']; ?>" /> <?php else: ?> <img src="__PUBLIC__/skin/img/icon/head-3.jpg" />
						<?php endif; ?>
	        					</div>
	        					<div class="item-title"><?php echo $user['nickname']; ?></div>
	        				</div>
	        				<div class="item-text">
	        					<a href="<?php echo url('news'); ?>">
	        						<?php if(!(empty($has_message) || (($has_message instanceof \think\Collection || $has_message instanceof \think\Paginator ) && $has_message->isEmpty()))): ?><span class="news-badge"></span><?php endif; ?>
									<p class="icon-message"></p>
									<p>消息</p>
								</a>
								<a href="<?php echo url('personal'); ?>">
									<p class="icon-shezhi"></p>
									<p>设置</p>
								</a>
								<a href="<?php echo url('collection'); ?>">
									<p class="icon-shoucang"></p>
									<p>收藏</p>
								</a>
	        				</div>
	        			</div>
	        		</div>
	        	</div>
	        </div>
        
	        <div class="weui-panel weui-top">
				<div class="weui-panel__bd">
					<div class="weui-media-box weui-media-box_small-appmsg">
						<div class="weui-cells">
							<a class="weui-cell weui-cell_access" href="<?php echo url('order'); ?>">
								<div class="weui-cell__hd my-icon"><img src="__PUBLIC__/skin/img/icon/my-icon.png"/></div>
								<div class="weui-cell__bd weui-cell_primary">
									<p>我的订单</p>
								</div>
								<span class="weui-cell__ft"></span>
							</a>
							<a class="weui-cell weui-cell_access" href="<?php echo url('adopt/recording'); ?>">
								<div class="weui-cell__hd my-icon"><img src="__PUBLIC__/skin/img/icon/my-icon2.png"/></div>
								<div class="weui-cell__bd weui-cell_primary">
									<p>认养记录</p>
								</div>
								<span class="weui-cell__ft"></span>
							</a>
							<a class="weui-cell weui-cell_access" href="<?php echo url('book/orderlist'); ?>">
								<div class="weui-cell__hd my-icon"><img src="__PUBLIC__/skin/img/icon/my-icon3.png"/></div>
								<div class="weui-cell__bd weui-cell_primary">
									<p>订桌清单</p>
								</div>
								<span class="weui-cell__ft"></span>
							</a>
							<a class="weui-cell weui-cell_access" href="<?php echo url('cardpack'); ?>">
								<div class="weui-cell__hd my-icon"><img src="__PUBLIC__/skin/img/icon/my-icon4.png"/></div>
								<div class="weui-cell__bd weui-cell_primary">
									<p>卡包</p>
								</div>
								<span class="weui-cell__ft"></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!--导航-->
		<nav class="footer">
			<a href="<?php echo url('adopt/index'); ?>">
				<div class="icon">
		            <span class="icon-ziyuan5"></span>
		        </div>
				<p class="tetx">认养</p>
		    </a>
		    <a href="<?php echo url('shop/index'); ?>">
				<div class="icon">
		            <span class="icon-sc_w"></span>
		        </div>
				<p class="tetx">商城</p>
		    </a>
		    <a href="/">
				<div class="icon">
		            <span class="icon-ziyuan6"></span>
		        </div>
				<p class="tetx">首页</p>
		    </a>
		    <a href="<?php echo url('Book/index'); ?>">
				<div class="icon">
		            <span class="icon-ziyuan7"></span>
		        </div>
				<p class="tetx">预定</p>
		    </a>
		    <a href="<?php echo url('users/index'); ?>" class="active">
				<div class="icon">
		            <span class="icon-ziyuan3"></span>
		        </div>
				<p class="tetx">我的</p>
		    </a>
		</nav>

		<!-- js -->
		<script src="__PUBLIC__/skin/js/jquery.min.js"></script>
		<script src="__PUBLIC__/skin/js/fastclick.js"></script>
		<script>
			$(function() {
				//消除延迟
				FastClick.attach(document.body);
			});
		</script>
		<script src="__PUBLIC__/skin/js/jquery-weui.js"></script>
	</body>
</html>