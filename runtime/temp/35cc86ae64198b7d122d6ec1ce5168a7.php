<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/book/expired.html";i:1516084570;}*/ ?>
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
		<title>已过期订单详情</title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/book.css" rel="stylesheet">
		<style>
			.weui-cell__ft input{ text-align: right;}
		</style>

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>

		<header class="header">
			<a class="icon-jiantou-left" href="orderlist.html"></a>
			<div class="header-title">已过期订单详情</div>
		</header>

		<div class="settlement">

			<div class="weui-cells weui-top">
				<div class="weui-cell">
					<div class="weui-cell__hd"><label class="weui-label">订单号</label></div>
					<div class="weui-cell__bd gray"><?php echo $order['order_sn']; ?></div>
				</div>
			</div>
			<hr>
			<div class="weui-cells weui-top">
				<div class="weui-cell">
					<div class="weui-cell__hd"><label class="weui-label">联系人</label></div>
					<div class="weui-cell__bd gray"><?php echo $order['name']; ?></div>
				</div>
				<div class="weui-cell">
					<div class="weui-cell__hd"><label class="weui-label">联系电话</label></div>
					<div class="weui-cell__bd gray" id="tel"><?php echo $order['mobile']; ?></div>
				</div>
				<div class="weui-cell">
					<div class="weui-cell__hd"><label class="weui-label">特别要求</label></div>
					<div class="weui-cell__bd gray"><?php echo $order['user_note']; ?></div>
				</div>
			</div>
			<hr>
			<div class="weui-cells weui-top">
				<div class="weui-cell">
					<div class="weui-cell__hd"><label class="weui-label">订单信息</label></div>
					<div class="weui-cell__bd gray"><?php echo $order['seat']; ?></div>
				</div>
				<div class="weui-cell">
					<div class="weui-cell__hd"><label class="weui-label">用餐人数</label></div>
					<div class="weui-cell__bd gray" id="tel"><?php echo $order['people_num']; ?>人</div>
				</div>
				<div class="weui-cell">
					<div class="weui-cell__hd"><label class="weui-label">用餐时间</label></div>
					<div class="weui-cell__bd gray"><?php echo $order['at_time']; ?></div>
				</div>
			</div>
			<hr>
			<div class="sticky-enabled">
				<div class="Lookup">
					<span>名称</span>
					<span>数量</span>
					<span>金额</span>
				</div>
				<div id="firstpane" class="menu_list">
					<?php if(is_array($order['order_combo']) || $order['order_combo'] instanceof \think\Collection || $order['order_combo'] instanceof \think\Paginator): $i = 0; $__LIST__ = $order['order_combo'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
					<p class="menu_head current">
				    	<span><?php echo $vo['goods_name']; ?></span>
				    	<span>x<?php echo $vo['goods_num']; ?></span>
				    	<span><?php echo $vo['goods_price']; ?></span>
				    </p>
				    <div class="menu_body Lookup-list"<?php if($i == '1'): ?> style="display:block"<?php else: ?> style="display:none"<?php endif; ?>>
					    <span><?php echo $vo['goods_name']; ?>有：<?php echo $vo['goods_content']; ?></span>
	                </div>
	                <?php endforeach; endif; else: echo "" ;endif; ?>
				    
	            </div>
				<ul class="Lookup-list" id="Lookup-list">
					<?php if(is_array($order['order_goods']) || $order['order_goods'] instanceof \think\Collection || $order['order_goods'] instanceof \think\Paginator): $i = 0; $__LIST__ = $order['order_goods'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
					<li>
						<div class="btl"><?php echo $vo['goods_name']; ?></div>
						<div class="btl2">x<?php echo $vo['goods_num']; ?></div>
						<div class="btl3"><?php echo $vo['goods_price']; ?></div>
					</li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
			<hr>
			
			<div class="weui-cells weui-top">
			    <div class="weui-cell">
				    <div class="weui-cell__bd"><p>预付</p></div>
				    <div class="weui-cell__ft">
				    	<?php if($is_full == '2'): ?>预付订金20%<?php else: ?>全额付款<?php endif; ?>
				    </div>
			    </div>
			    <div class="weui-cell">
					<div class="weui-cell__bd">红包</div>
					<div class="weui-cell__ft"><small>-￥</small><span><?php echo $order['coupon_red']; ?></span></div>
				</div>
				<div class="weui-cell">
					<div class="weui-cell__bd">折扣券</div>
					<div class="weui-cell__ft"><small>-￥</small><span><?php echo $order['coupon_discount']; ?></span></div>
				</div>
				<div class="weui-cell">
					<div class="weui-cell__bd">订单总价</div>
					<div class="weui-cell__ft">
						<div class="originalprice">
							商品总价：
							<span class="original-text"><small>￥</small><span><?php echo $order['order_amount']; ?></span></span>
					    </div>
						<div class="originalprice">
							实付
							<span class="red">
						        <small>￥</small><span><?php echo $order['order_amount']; ?></span>
						    </span>
						</div>
					</div>
					
				</div>
			
				<div class="weui-cell">
					<div class="weui-cell__bd">订单状态</div>
					<div class="weui-cell__ft red"><?php echo $order['order_status']; ?></div>
				</div>
				<!-- <div class="weui-cell">
					<div class="weui-cell__bd">订单状态</div>
					<div class="weui-cell__ft red">逾时未付款</div>
				</div> -->
			</div>
            <hr>
			<div class="weui-cells weui-top">
				<div class="weui-cell weui-bd">
					<div class="weui-cell__bd">订单规则：</div>
				</div>
				<div class="weui-cell weui-bd" style="text-indent: 2em;">为让各位亲可享受极致美味，所有食材均按订单需要、用餐前24小时内进行生产（采摘、屠宰等），您的退定（即取消订单）会给我们带来极大的损失，对此我们将象征性收取部分费用。</div>
				<div class="weui-cell weui-bd">1、全额支付订单的退定规则：</div>
				<div class="weui-cell weui-bd" style="text-indent: 2em;">(1)用户在距离预定用餐时间24小时（含）以上取消订单的，无责，全额退款。</div>
				<div class="weui-cell weui-bd" style="text-indent: 2em;">(2)用户在距离预定用餐时间12（含）-24小时（不含）内取消订单的，系统将收取全额订单金额的5%，余款原路退回。</div>
				<div class="weui-cell weui-bd" style="text-indent: 2em;">(3)用户在距离预定用餐时间6（含）-12（不含）小时内取消订单的，系统将收取全额订单金额的10%，余款原路退回。</div>
				<div class="weui-cell weui-bd" style="text-indent: 2em;">(4)用户在距离预定用餐时间6小时内取消订单的，系统将收取全额订单金额的15%，余款原路退回。</div>
				<div class="weui-cell weui-bd" style="text-indent: 2em;">(5)用户超过预定用餐时间40分钟未能到场用餐的，该订单将自动失效，系统将收取全额订单金额的15%，余款原路退回。</div>
				<div class="weui-cell weui-bd">2、支付定金订单的退定规则：</div>
				<div class="weui-cell weui-bd" style="text-indent: 2em;">(1)用户在距离预定用餐时间24小时（含）以上取消订单的，无责，全额退回预付定金。</div>
				<div class="weui-cell weui-bd" style="text-indent: 2em;">(2)用户在24小时内取消订单的，预付定金不予退回。</div>
				<div class="weui-cell weui-bd">3、退定周期：1-5个工作日</div>
				
			</div>

			
		</div>
		
		

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
				
				//隐藏电话4位数
				var tel = $('#tel').html();
				var mtel = tel.substr(0, 3) + '****' + tel.substr(8);
				//$('#tel').text(mtel);
				
				//产品
				/*var listDom = document.getElementById("Lookup-list");
				for(var i = 0; i < 6; i++) {
					
					var str = '<div class="btl">通过不透明度的变化通过不透明度的变化通过不透明度的变化</div>';
						str += '<div class="btl2">x1</div>';
						str += '<div class="btl3">200</div>';
						
					var liDom = document.createElement("li");
					liDom.innerHTML = str;
					listDom.appendChild(liDom);
				}*/
				
				//手风琴展示套餐
				$("#firstpane .menu_body:eq(0)").show();
				$("#firstpane .menu_head").click(function() {
					$(this).addClass("current").next(".menu_body").slideToggle(300).siblings(".menu_body").slideUp("slow");
					$(this).siblings().removeClass("current");
				});
				$("#secondpane .menu_body:eq(0)").show();
				$("#secondpane .menu_head").mouseover(function() {
					$(this).addClass("current").next(".menu_body").slideDown(500).siblings(".menu_body").slideUp("slow");
					$(this).siblings().removeClass("current");
				});


			});
		</script>

	</body>

</html>