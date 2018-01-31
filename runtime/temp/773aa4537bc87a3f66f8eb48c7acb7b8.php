<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/cart/index.html";i:1516084570;}*/ ?>
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
		<link rel="shortcut icon" type="image/ico" href="__PUBLIC__/skin/favicon.ico" />
		<!-- 添加 favicon icon -->
		<!-- sns 社交标签 begin -->
		<!-- 参考微博API -->
		<meta property="og:type" content="类型" />
		<meta property="og:url" content="URL地址" />
		<meta property="og:title" content="标题" />
		<meta property="og:image" content="图片" />
		<meta property="og:description" content="描述" />
		<title>购物车</title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/cart.css" rel="stylesheet">
		<style>
			.weui-popup__overlay{
				opacity: 1;
			}
		</style>

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>

		<header class="header">
			<a class="icon-jiantou-left" href="javascript:history.go(-1)"></a>
			<div class="header-title">购物车</div>
			<a class="txte-right" href="javascript:;">编辑</a>
		</header>

		<div class="cart">
			
			<div class="Dataempty">
				<div class="Dataempty_img">
					<img src="__PUBLIC__/skin/img/icon/carts.png" />
					<p class="txte">暂无数据，快去逛逛吧！</p>
					<div class="strollaround">
						<a href="<?php echo url('Shop/index'); ?>" class="cart-btn">去逛逛</a>
					</div>
				</div>
			</div>

			<div class="con">

				<?php if(count($carts) >= 1){ ?>
				<div class="content">
					<ul>

						<?php if(is_array($carts) || $carts instanceof \think\Collection || $carts instanceof \think\Paginator): $i = 0; $__LIST__ = $carts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<li class="clearfix">
							<div class="label fl">
								<label>
					    			<input type="checkbox" data-id="<?php echo $v['id']; ?>"/>
					    			<img src="__PUBLIC__/skin/img/icon/off.png"/>
					    		</label>
							</div>
							<div class="img"><img src="<?php echo $v['original_img']; ?>" /></div>
							<div class="text">
								<p class="overflow"><?php echo $v['goods_name']; ?></p>
							</div>
							<div class="text">
								<p class="clearfix top">
									<small class="red">￥</small>
									<span class="red"><?php echo $v['shop_price']; ?></span>
									<span class="m-spinner">
				    					<input type="button" class="btn1" data-id="<?php echo $v['id']; ?>"/>
				    					<span class="number"><?php echo $v['goods_num']; ?></span>
									    <input type="button" class="btn2" data-count="<?php echo $v['store_count']; ?>" data-id="<?php echo $v['id']; ?>"/>
									</span>
								</p>
							</div>
						</li>
						<?php endforeach; endif; else: echo "" ;endif; ?>

						<!--<li class="clearfix">-->
							<!--<div class="label fl">-->
								<!--<label>-->
					    			<!--<input type="checkbox" checked="checked"/>-->
					    			<!--<img src="__PUBLIC__/skin/img/icon/on.png"/>-->
					    		<!--</label>-->
							<!--</div>-->
							<!--<div class="img"><img src="__PUBLIC__/skin/img/icon/head-3.jpg" /></div>-->
							<!--<div class="text">-->
								<!--<p class="overflow">通过不透明度的变化通过不透明度的变化通过不透明度的变化</p>-->
							<!--</div>-->
							<!--<div class="text">-->
								<!--<p class="over">颜色：红，尺寸：XL</p>-->
								<!--<p class="clearfix top">-->
									<!--<small class="red">￥</small>-->
									<!--<span class="red">10</span>-->
									<!--<span class="m-spinner">-->
				    					<!--<input type="button" value="" class="btn1" />-->
				    					<!--<span class="number">1</span>-->
									    <!--<input type="button" value="" class="btn2" />-->
									<!--</span>-->
								<!--</p>-->
							<!--</div>-->
						<!--</li>-->
					</ul>
					<p class="total">一共<number></number>件商品:<span></span></p>
				</div>
				<?php }?>

			</div>

			<!--结算-->
			<div class="bottom">
				<div class="bottom-label">
					<label>
		    			<input type="checkbox"/>
		    			<img src="__PUBLIC__/skin/img/icon/off.png" />
		    			<b>全选</b>
		    		</label>
				</div>
				<div class="bottom-left">合计:
					<span>￥</span>
					<a href="#" class="sett yellow-color">结算</a>
				</div>
			</div>

			<!--删除-->
			<div class="bottom fixed" style="display: none;">
				<div class="fr">
					<button class="delete yellow-color">删除</button>
				</div>
			</div>

		</div>


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
		<script src="__PUBLIC__/skin/js/cart.js"></script>
		

	</body>

</html>