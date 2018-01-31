<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/product/details.html";i:1516960938;}*/ ?>
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
		<link rel="apple-touch-startup-image" sizes="640x960" href="__PUBLIC__/skin/__PUBLIC__/skin/img/App-ios-logo-152x152.png" />
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
		<title>九月新农园-产品详情</title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/shopping.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<style>
			.swiper-container-horizontal>.swiper-pagination-bullets {
			    bottom: 10px;
			}
			.weui-popup__overlay{
				opacity: 1;
			}
		</style>

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>

		<!-- loading -->
		<section class="mask-dialog">
			<div class="load">
				<img src="__PUBLIC__/skin/img/icon/loading.gif" />
				<div class="loadingtxt">加载中··</div>
			</div>
		</section>

		<div class="mb-mall">

			<!--顶部导航-->
			<div class="anchor_layer" id="navHeight">
				<a href="javascript:history.go(-1)" class="icon-jiantou-left"></a>
				<div class="nav_wrap" id="nav_wrap">
				    <div class="scrollSearchPro">
				    	<a class="tab active" href="#tab1">商品</a>
					    <a class="tab" href="#tab2">详情</a>
				    </div>
				</div>
			</div>

			<div id="tab1">

				<!--banner-->
				<section class="swiper-container">
					<div class="swiper-wrapper">
						<?php if(is_array($images) || $images instanceof \think\Collection || $images instanceof \think\Paginator): $i = 0; $__LIST__ = $images;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<div class="swiper-slide"><img src="<?php echo $v['image_url']; ?>" /></div>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</div>
					<div class="swiper-pagination"></div>
				</section>
				<div class="product-title">
					<h2><?php echo $goods['goods_name']; ?></h2>
					<div class="price">
						<div class="current-price">
							<span class="current-price"><small>¥</small><span><?php echo $goods['shop_price']; ?></span></span>
							<span class="aui-slide-item-mrk"><small>¥</small><?php echo $goods['market_price']; ?></span>
						</div>
					</div>
					<div class="module-adds">
						<span>全国包邮</span>
						<span>库存  <?php echo $goods['store_count']; ?></span>
					</div>
				</div>
				<div class="weui-cells weui-top">
				    <a class="weui-cell weui-cell_access open-popup" href="javascript:;" data-target="#coupons">
					    <div class="weui-cell__bd">
					    	<p>领券
					    		<span class="red">
					    		<?php if(is_array($couponlist) || $couponlist instanceof \think\Collection || $couponlist instanceof \think\Paginator): $i = 0; $__LIST__ = $couponlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($i < 3): if($vo['type'] == 7): ?>
					    		<?php echo $vo['money']; ?>折扣券
					    		<?php else: ?>
								满<?php echo $vo['condition']; ?>元减<?php echo $vo['money']; ?>元
								<?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
					    		</span>
					    	</p>
					    </div>
					    <div class="weui-cell__ft"></div>
				    </a>
				</div>
				<hr>

				<?php if($goods['cat_id'] != 1): ?>
				<div class="weui-cells weui-top">
				    <a class="weui-cell weui-cell_access open-popup" href="javascript:;" data-target="#parameter">
					    <div class="weui-cell__bd"><p>产品参数</p></div>
					    <div class="weui-cell__ft"></div>
				    </a>
				</div>
				<hr>
				<?php endif; if($goods['cat_id'] == 1): ?>
				<div class="weui-panel weui-panel_access weui-top">
					<div class="weui-panel__hd">礼盒构成</div>
					<div class="weui-panel__bd">

						<?php if(is_array($entry) || $entry instanceof \think\Collection || $entry instanceof \think\Paginator): $i = 0; $__LIST__ = $entry;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
							<div class="weui-media-box__hd w150">
								<img class="weui-media-box__thumb" src="<?php echo $v['pic']; ?>" alt="">
							</div>
							<div class="weui-media-box__bd">
								<h4 class="weui-media-box__title"><?php echo $v['name']; ?></h4>
								<p class="red"><small>¥</small><span><?php echo $v['price']; ?></span></p>
								<p class="weui-media-box__desc">净重：<?php echo $v['weight']; ?>g</p>
							</div>
						</a>
						<?php endforeach; endif; else: echo "" ;endif; ?>

					</div>
				</div>
				<hr>
				<?php endif; ?>

			</div>

			<div id="tab2">
				<div class="pro-detail">
					<?php echo $goods['goods_content']; ?>
				</div>
			</div>

            <!--购物导航-->
            <footer class="product-fixed">
				<div class="product-cart">
					<a href="/">
						<span class="icon-ziyuan6"></span>
						<span class="focus-info">首页</span>
					</a>
					<a href="javascript:;" class='show-toast <?php if($collect == '1'): ?>date-dz-z-click<?php endif; ?>' data-id="<?php echo $goods['goods_id']; ?>">
						<span class="icon-shoucang <?php if($collect == '1'): ?>red<?php endif; ?>"></span>
						<span class="focus-info">收藏</span>
					</a>
					<a href="<?php echo url('Cart/index'); ?>">
						<?php if($cartNum != '0'): ?>
						<span class="weui-badge"><?php echo $cartNum; ?></span>
						<?php endif; ?>
						<span class="icon-carttwo"></span>
						<span class="focus-info">购物车</span>
					</a>
				</div>
				<div class="product-list">
					<input type="button" class="yellow-color open-popup" data-target="#join_cart" value="加入购物车"></input>
					<input type="button" class="red-color open-popup" onclick='addCart("<?php echo $goods['goods_id']; ?>","1","1");' value="立即购买"></input>
				</div>
			</footer>

		</div>


		<!--优惠券弹窗-->
		<div id="coupons" class="weui-popup__container popup-bottom">
		    <div class="weui-popup__overlay"></div>
		    <div class="weui-popup__modal">
		    	<div class="tool-bar">
		    		<h1>领取优惠券</h1>
					<a href="javascript:;" class="picker-button close-popup fr">关闭</a>
				</div>
				<ul class="tool-box">
					<?php if(is_array($couponlist) || $couponlist instanceof \think\Collection || $couponlist instanceof \think\Paginator): $i = 0; $__LIST__ = $couponlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
					<li>
						<a href="javascript:;">
							<div class="coupon-group">
								<div class="<?php switch($vo['type']): case "5": ?>envelope<?php break; case "6": ?>voucher<?php break; case "7": ?>Discount<?php break; default: endswitch; ?>">
									<div class="coupon-box coupon-<?php switch($vo['type']): case "5": ?>envelope<?php break; case "6": ?>voucher<?php break; case "7": ?>Discount<?php break; default: endswitch; ?>"> <h1><small><?php if($vo['type'] != '7'): ?>￥<?php else: ?>折扣<?php endif; ?></small><?php echo $vo['money']; ?></h1>
										<p>订单满<?php echo $vo['condition']; ?>使用</p>
									</div>
									<div class="coupon-right">
                                        <p><?php echo $vo['type_name']; ?></p>
                                        <p><?php echo $vo['use_start_time']; ?>-<?php echo $vo['use_end_time']; ?></p>
									</div>
									<input type="button" data-cid="<?php echo $vo['id']; ?>" class="Toreceive <?php switch($vo['type']): case "5": ?>envelope<?php break; case "6": ?>voucher<?php break; case "7": ?>Discount<?php break; default: endswitch; ?>-btn addcar Have" value="领取"></input>
								</div>
							</div>
						</a>
					</li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
		    </div>
		</div>


		<!--参数弹窗-->
		<div id="parameter" class="weui-popup__container popup-bottom">
		    <div class="weui-popup__overlay"></div>
		    <div class="weui-popup__modal">
                <div class="tool-bar">
		    		<h1>产品参数</h1>
					<a href="javascript:;" class="picker-button close-popup fr">关闭</a>
				</div>
				<div class="tool-box">
					<div class="weui-cells">

						<?php if(is_array($sizes) || $sizes instanceof \think\Collection || $sizes instanceof \think\Paginator): $i = 0; $__LIST__ = $sizes;if( count($__LIST__)==0 ) : echo "<div class='weui-cell'>暂无数据</div>" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
					    <div class="weui-cell">
						    <div class="weui-cell__bd"><?php echo $v['name']; ?></div>
						    <div class="weui-cell__ft"><?php echo $v['val']; ?></div>
					    </div>
						<?php endforeach; endif; else: echo "<div class='weui-cell'>暂无数据</div>" ;endif; ?>

					</div>
				</div>
		    </div>
		</div>

		<!--加入购物车-->
		<form action="" id="buy_cart">
			<input type="hidden" name="goods_id" value="<?php echo $goods['goods_id']; ?>">
			<div id="join_cart" class="weui-popup__container popup-bottom">
				<div class="weui-popup__overlay"></div>
				<div class="weui-popup__modal">
					<div class="modal-content">
						<div class="weui-msgpd commodity">
							<div class="shing">
								<div class="tu fl">
									<img src="__PUBLIC__/skin/img/img.jpg"/>
								</div>
								<div class="you fr">
									<p class="tit"><?php echo $goods['goods_name']; ?></p>
									<p class="ewsqw"><small>￥</small><i class="price"><?php echo $goods['shop_price']; ?></i></p>
								</div>
							</div>
							<div class="media-box">
								<span class="fl">购买数量</span>
								<span class="spinner">
									<button class="jian" type="button">-</button>
									<input type="text" id="goods_num" class="shuliang" name="goods_num" value="1">
									<button class="jia" type="button">+</button>
								</span>
							</div>
						</div>
						<div class="product-function mt100">
							<button type="button" class="yellow-color weui-popup__overlay" onclick="addCart('','','0');" style="width: 100%">确定</button>
						</div>
					</div>
				</div>
			</div>
		</form>

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
		<script src="__PUBLIC__/skin/js/lazyload.min.js"></script>
		<script src="__PUBLIC__/skin/js/swiper.min.js"></script>
		<script src="__PUBLIC__/skin/js/common.js"></script>
		<!--<script src="js/car.js"></script>-->
		<script>
			$(function() {
				//banner
				var swiper = new Swiper('.swiper-container', {
					loop: true,
					autoplay: true,
			        pagination: {
			            el: '.swiper-pagination',
			        },
			    });

			    //预加载
				$("img.lazy").lazyload({
					effect: "fadeIn"
				});

				//收藏产品
				$('.show-toast').click(function(){
					var user_id = "<?php echo $user_id; ?>";
					if(!user_id){
						$.confirm("请先登陆，再收藏！", "", function() {
							window.open('<?php echo url("login/index"); ?>', "_self")
						}, function() {
							$.toast("取消了登陆", "text");
							return ;
						});
					}
					else{
						var collect = $(this),
							goods_id = collect.data('id');

						$.ajax({
	                        type : "POST",
	                        url:"<?php echo url('product/collect'); ?>",
	                        data : {'goods_id':goods_id},
	                        dataType:'json',
	                        success: function(data){
	                            if(data.code){
							        if(collect.is('.date-dz-z-click')){
							        	$.toast('取消收藏', 'cancel');
							            collect.removeClass('date-dz-z-click');
							            collect.find('.icon-shoucang').removeClass('red');
							        } else{
							        	$.toast('收藏成功', 'success');
							            collect.addClass('date-dz-z-click');
							            collect.find('.icon-shoucang').addClass('red');
							        }
								}else{
	                                $.toast(data.msg, "cancel")
	                            }
	                        }
	                    });
                    }
			    })

                //领取券后按钮变灰
				$(".addcar").click(function(event) {
					var addcar = $(this),
						cid = addcar.data('cid');


					$.ajax({
                        type : "POST",
                        url:"<?php echo url('Coupon/draw'); ?>",
                        data : {'cid':cid},
                        dataType:'json',
                        success: function(data){
                            if(data.code){
					            $.toast("已领取", "text" )
								addcar.val('已领取');
							}else{
                                $.toast(data.msg, "text" )
								addcar.val('领取失败');
                            }
                        }
                    });

					addcar.css("cursor", "default").removeClass('envelope-btn').unbind('click');
					addcar.css("cursor", "default").removeClass('voucher-btn').unbind('click');
					addcar.css("cursor", "default").removeClass('Discount-btn').unbind('click');
				});

				//数量加减
				$(".spinner button").click(function(event) {
			        var elem = event.target;
			        if ($(elem).text() == "+") {
			            var snum = parseInt($(elem).prev().val());
			            var store_count = "<?php echo $goods['store_count']; ?>";
                        $(elem).prev().val(snum + 1);
			            if (snum >= parseInt(store_count)) {
                            $(elem).prev().val(store_count);
			                $.toast("超过库存数量", "text" )
			            }
			        } else {
			            var snum = parseInt($(elem).next().val());
			            $(elem).next().val(snum - 1);
			            if (snum == 1) {
			                $(elem).next().val(1);
			                $.toast("购买商品必须为1", "text")
			            }
			        }
			    });

			    //锚点导航
			    $(document).ready(function(){
					var navHeight= $("#navHeight").offset().top;
					var navFix=$("#scrollSearchPro");
					$(window).scroll(function(){
						if($(this).scrollTop()>navHeight){
							navFix.addClass("navFix");
						}
						else{
							navFix.removeClass("navFix");
						}
					})
				})
			    //内容信息导航锚点
			    $('.scrollSearchPro').navScroll({
				    mobileDropdown: true,
				    mobileBreakpoint: 768,
				    scrollSpy: true
				});


			});

			//loading
			var $preloader=$('.mask-dialog'),
			$spinner=$preloader.find('.spinner');
	        $spinner.fadeOut();
			$preloader.delay(500).fadeOut('slow');
			/*$.showLoading();
			setTimeout(function() {
				$.hideLoading();
			}, 3000)*/

		</script>
	</body>

</html>