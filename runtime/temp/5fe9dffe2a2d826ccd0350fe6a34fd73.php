<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/cart/details.html";i:1516084570;}*/ ?>
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
		
		<title>订单详情</title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<style>
			.weui-popup__overlay {
				opacity: 1;
			}
		</style>

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>

		<header class="header">
			<a class="icon-jiantou-left" href="<?php echo url('Users/order'); ?>"></a>
			<div class="header-title">订单详情</div>
		</header>

		<div class="checkorder">

			<?php if($orders['pay_status'] == 1): ?>
			<div class="weui-cells bjlan weui-top">
				<a class="weui-cell weui-cell_access" href="<?php echo url('users/logistics',array('order_id'=>$orders['order_id'])); ?>">
					<div class="weui-cell__bd">
						<p>
							<?php switch($orders['shipping_status']): case "0": ?>待发货<?php break; case "1": ?>已发货<?php break; default: ?>暂无数据
							<?php endswitch; ?>
						</p>
						<p>
						物流信息：
						<?php switch($orders['order_status']): case "2": ?>已收货<?php break; default: ?>订单处理中
						<?php endswitch; ?>
						</p>
						<p>时间：<?php echo date("Y-m-d H:i:s",$orders['confirm_time']); ?></p>
					</div>
					<div class="weui-cell__ft"></div>
				</a>
			</div>
			<?php endif; ?>

			<div class="weui-cells weui-top">
				<div class="weui-cell">
					<div class="weui-cell__bd">
						<div class="box-item">
							<div class="box-item-bd"><?php echo $orders['consignee']; ?></div>
							<div class="box-item-ft tel"><?php echo $orders['mobile']; ?></div>
						</div>
						<div class="box-item">
							<div class="box-item-bd gree"><?php echo $orders['address']; ?></div>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<ul class="order-list">
				<li>
					<div class="order-list-hd"><span>单号：<em><?php echo $orders['order_sn']; ?></em></span></div>

					<?php if(is_array($order_goods) || $order_goods instanceof \think\Collection || $order_goods instanceof \think\Paginator): $i = 0; $__LIST__ = $order_goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
					<a href="<?php echo url('product/details',array('goods_id'=>$v['goods_id'])); ?>">
						<div class="order-box">
							<div class="order-list-bd">
								<div class="order-img"><img src="<?php echo $v['original_img']; ?>" alt=""></div>
								<div class="order-text">
									<div class="order-text-width">
										<h1><?php echo $v['goods_name']; ?></h1></div>
									<div class="order-text-right">
										<div class="order-right-txte"><span><small>¥</small><?php echo $v['goods_price']; ?></span><br><span class="order-text-mrk"><small>¥</small><?php echo $v['market_price']; ?></span></div>
									</div>
									<div class="clear">
										<div class="wy-pro-pri fl"><?php echo $v['goods_remark']; ?></div>
										<div class="pro-amount fr"><span>×<em><?php echo $v['goods_num']; ?></em></span></div>
									</div>
									<?php if($v['is_send'] == 1): ?>
									<div >
										<a href="javascript:;" data-id="<?php echo $v['goods_id']; ?>" class="check-btn Unsubscribe fr">退订</a>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</a>
					<?php endforeach; endif; else: echo "" ;endif; ?>
					
					<div class="weui-cells weui-top">
						<div class="weui-cell">
							<div class="weui-cell__bd">订单总价</div>
							<div class="weui-cell__ft"><small>￥</small><span><?php echo $orders['goods_price']; ?></span></div>
						</div>
						<div class="weui-cell weui-cell_access">
							<div class="weui-cell__bd">红包</div>
							<div class="set-cell-ft">-￥<?php echo $orders['coupon_red']; ?></div>
						</div>
						<div class="weui-cell weui-cell_access open-popup" data-target="#discountcoupons">
							<div class="weui-cell__bd">折扣券</div>
							<div class="set-cell-ft"><?php echo $orders['coupon_rebate']; ?>折</div>
						</div>
						<!--<div class="weui-cell weui-cell_access open-popup" data-target="#exchangevoucher">
							<div class="weui-cell__bd">兑换券</div>
							<div class="set-cell-ft">-￥3.0</div>
						</div>-->
						<div class="weui-cell">
							<div class="weui-cell__bd">实付款</div>
							<div class="weui-cell__ft red"><small>￥</small><span><?php echo $orders['order_amount']; ?></span></div>
						</div>
					</div>
				</li>
			</ul>

			<?php if($orders['pay_status'] == 1): ?>
			<div class="weui-cells">
				<div class="weui-cell weui-bd">
					<div class="weui-cell__bd">支付方式：<span><?php echo $orders['pay_name']; ?></span></div>
				</div>
				<div class="weui-cell weui-bd">
					<div class="weui-cell__bd">支付交易号：<span></span></div>
				</div>
				<div class="weui-cell weui-bd">
					<div class="weui-cell__bd">成交时间：<span><?php echo date("Y-m-d H:i:s",$orders['add_time']); ?></span></div>
				</div>
				<div class="weui-cell weui-bd">
					<div class="weui-cell__bd">付款时间：<span><?php echo date("Y-m-d H:i:s",$orders['pay_time']); ?></span></div>
				</div>
			</div>
			<?php endif; ?>

			<hr>
			<div class="check-cells">
				<div class="check-right">
					<?php if($orders['order_status'] == 2): ?>
					<a href="#" class="check-btn cancelall">整单退订</a>
					<?php endif; if($orders['shipping_status'] > 0 && $orders['order_status'] == 1){ ?>
					<a href="#" class="check-btn red">确认收货</a>
					<?php }if($orders['pay_status'] == 0): ?>
					<a href="#" class="check-btn red">立即付款</a>
					<?php if($orders['shipping_status'] == 0): ?>
					<a href="javascript:;" class="check-btn cancel">取消订单</a>
					<?php endif; endif; ?>
				</div>
			</div>
			<hr>
			
		</div>

		<!-- js -->
		<script src="__PUBLIC__/skin/js/jquery.min.js"></script>
		<script src="__PUBLIC__/skin/js/mescroll.js"></script>
		<script src="__PUBLIC__/skin/js/fastclick.js"></script>
		<script>
			$(function() {
				//消除延迟
				FastClick.attach(document.body);
				
				$(".promotion li").click(function(){
				    $(this).addClass("active").siblings("li").removeClass("active");
				})
				
				//Unsubscribe
				$('.cancelall').click(function(event) {
                    var order_id = "<?php echo $orders['order_id']; ?>";
					$.confirm("您确定整单退订吗？", "整单退订", function() {
                        window.open("/index.php/wap/Users/returns?order_id="+order_id, "_self");
					});
				});
				
				$('.Unsubscribe').click(function(event) {
					var order_id = "<?php echo $orders['order_id']; ?>";
					var goods_id = $(this).data('id');
					$.confirm("联系客服：<a class='red' href='tel:020-12345678'>020-215554487</a><br>微信客服：dgedge", "申请退订", function() {
						window.open("/index.php/wap/Users/returns?order_id="+order_id+"&goods_id="+goods_id, "_self");
					});
				});

                $('.cancel').click(function(event) {
                    $.confirm("您确定取消订单吗？", "取消订单", function() {
                        window.open('#', "_self")
                    });
                });
			});
		</script>
		<script src="__PUBLIC__/skin/js/jquery-weui.js"></script>

	</body>

</html>