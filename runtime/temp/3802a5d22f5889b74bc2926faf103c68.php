<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/cart/order.html";i:1516084570;}*/ ?>
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
		<meta name="msapplication-TileImage" content="__PUBLIC__/skin/icon.png" />
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
		<title>确认订单</title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<style>
			.weui-cell__ft input{ text-align: right;}
		</style>

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>

		<header class="header">
			<a class="icon-jiantou-left" href="javascript:history.go(-1)"></a>
			<div class="header-title">确认订单</div>
		</header>

		<form action="<?php echo url('addOrder'); ?>" method="post">
			<div class="confirmorder">
				<input type="hidden" name="red_id" value="">
				<input type="hidden" name="red_value" value="">
				<input type="hidden" name="rebate_id" value="">
				<input type="hidden" name="rebate_value" value="">
				<input type="hidden" name="goods_id" value="<?php echo \think\Request::instance()->get('goods_id'); ?>">
				<input type="hidden" name="goods_num" value="<?php echo \think\Request::instance()->get('num'); ?>">

				<div class="weui-cells__title red">温馨提示：鲜活易腐不适用七天无理由退货</div>
				<div class="weui-cells">
					<a class="weui-cell weui-cell_access" href="<?php echo url('users/address'); ?>">
						<div class="weui-cell__bd">
							<div class="box-item">
								<div class="box-item-bd"><?php echo $address['consignee']; ?></div>
								<input type="hidden" name="consignee" value="<?php echo $address['consignee']; ?>">
								<div class="box-item-ft tel"><?php echo $address['mobile']; ?></div>
								<input type="hidden" name="mobile" value="<?php echo $address['mobile']; ?>">
							</div>
							<div class="box-item">
								<div class="box-item-bd gree"><?php echo $address['address']; ?></div>
								<input type="hidden" name="address" value="<?php echo $address['address']; ?>">
							</div>
						</div>
						<div class="weui-cell__ft"></div>
					</a>
				</div>
				<hr>
				<ul class="order-list">
					<li>

						<?php if(is_array($order) || $order instanceof \think\Collection || $order instanceof \think\Paginator): $i = 0; $__LIST__ = $order;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<a href="#">
							<input type="hidden" name="cart_id[<?php echo $key; ?>]" value="<?php echo $v['cart_id']; ?>">
							<div class="order-box">
								<div class="order-list-bd">
									<div class="order-img">
										<img src="<?php echo $v['original_img']; ?>" alt="">
									</div>
									<div class="order-text">
										<div class="order-text-width">
											<h1><?php echo $v['goods_name']; ?></h1></div>
										<div class="order-text-right">
											<div class="order-right-txte"><span><small>¥</small><?php echo $v['shop_price']; ?></span><br><span class="order-text-mrk"><small>¥</small><?php echo $v['market_price']; ?></span></div>
										</div>
										<div class="clear">
											<div class="wy-pro-pri fl"><?php echo $v['goods_remark']; ?></div>
											<div class="pro-amount fr"><span>×<em><?php echo $v['goods_num']; ?></em></span></div>
										</div>
									</div>
								</div>
							</div>
						</a>
						<?php endforeach; endif; else: echo "" ;endif; ?>

						<div class="weui-cells weui-top">
							<?php if(strlen($red) > 2){?>
							<div class="weui-cell weui-cell_access">
								<div class="weui-cell__bd">红包</div>
								<div class="weui-cell__ft"><input class="weui-input" id="hb" type="text" value="请选择红包"></div>
							</div>
							<?php }if(strlen($rebate) > 2){?>
							<div class="weui-cell weui-cell_access">
								<div class="weui-cell__bd">折扣券</div>
								<div class="weui-cell__ft"><input class="weui-input" id="jkq" type="text" value="请选择折扣券"></div>
							</div>
							<?php }?>
							<!--<div class="weui-cell weui-cell_access">-->
							<!--<div class="weui-cell__bd">兑换券</div>-->
							<!--<div class="weui-cell__ft"><input class="weui-input" id="dhq" type="text" value="请选择兑换券"></div>-->
							<!--</div>-->
							<div class="weui-cell">
								<div class="weui-cell__bd">运费</div>
								<div class="weui-cell__ft">￥0.00</div>
							</div>
						</div>
					</li>
				</ul>

				<div class="confirmorder-foot">
					<div class="cart-mun">
						合计：<span class="red"><small>¥</small><span id="money"><?php echo $money; ?></span></span>
					</div>
					<input type="hidden" name="goods_price" value="<?php echo $money; ?>">
					<input type="hidden" name="order_amount" value="<?php echo $money; ?>">
					<div class="tjdd">
						<input type="submit" class="yellow-color" value="提交订单">
					</div>
				</div>
			</div>
		</form>

		<!-- js -->
		<script src="__PUBLIC__/skin/js/jquery.min.js"></script>
		<script src="__PUBLIC__/skin/js/mescroll.js"></script>
		<script src="__PUBLIC__/skin/js/fastclick.js"></script>
		<script>
			$(function() {
				//消除延迟
				FastClick.attach(document.body);
			});
		</script>
		<script src="__PUBLIC__/skin/js/jquery-weui.js"></script>
		<script>
			$(function(){
				var money = "<?php echo $money; ?>";
				//红包
				$("#hb").select({
					title: "请选择红包",
					items: <?php echo $red; ?>,
					onChange: function(d) {
                        $("input[name='red_id']").val(d.origins['0']['id']);
						$("input[name='red_value']").val(d.origins['0']['value']);
                        setMoney(money)
					},
//					onClose: function() {
//						console.log("close");
//					},
//					onOpen: function() {
//						console.log("open");
//					},
				});
				
				//折扣券
				$("#jkq").select({
					title: "请选择折扣券",
					items: <?php echo $rebate; ?>,
					onChange: function(d) {
                        $("input[name='rebate_id']").val(d.origins['0']['id']);
                        $("input[name='rebate_value']").val(d.origins['0']['value']);
                        setMoney(money)
					},
				});
				
//				//兑换券
//				$("#dhq").select({
//					title: "请选择兑换券",


//					items: ["购满100元可用20元", "购满100元可用20元", "购满100元可用40元", "购满100元可用60元", "购满100元可用80元", "购满100元可用100元"],
//					onChange: function(d) {
//						console.log(this, d);
//					},
//					onClose: function() {
//						console.log("close");
//					},
//					onOpen: function() {
//						console.log("open");
//					},
//				});

				
			});
			
			function setMoney(money) {
				var red = $("input[name='red_value']").val();
				var rebate = $("input[name='rebate_value']").val();
				if(rebate){
                    money = (money - red) * rebate/10;
				}else{
                    money = money - red;
				}
				$("#money").text(money.toFixed(2));
				$("input[name='order_amount']").val(money.toFixed(2));
            }
		</script>

	</body>

</html>