<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/shop/index.html";i:1516250741;}*/ ?>
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

        <title>九月新农园-商城</title>
		
		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/shopping.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<style>
			.swiper-container {
				width: 100%;
			}
			.swiper1 {
			    padding: 20px 0;
			}
			.swiper-slide1 {
				background-repeat: no-repeat;
				background-size: 100% 100%;
				width: 85%;
				border-radius: 15px;
                -webkit-border-radius: 15px;
                -webkit-box-shadow: 0 0px 30px rgba(0,0,0,0.3);
                box-shadow: 0 0px 30px rgba(0,0,0,0.3);

			}
			.swiper-slide2 {
				background-repeat: no-repeat;
				background-size: 100% 100%;
				width: 70%;
				border-radius: 15px;
                -webkit-border-radius: 15px;
                -webkit-box-shadow: 0 10px 20px rgba(0,0,0,0.1);
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
			}
			.swiper-pagination {
                position: relative;
            }
			.swiper-container-horizontal>.swiper-pagination-bullets {
			    bottom: 0px;
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
		
		<div class="mbtow">
			
			<!--banner-->
			<section class="swiper-container swiper1">
				<div class="swiper-wrapper">
					<?php if(is_array($banner) || $banner instanceof \think\Collection || $banner instanceof \think\Paginator): $i = 0; $__LIST__ = $banner;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
					<div class="swiper-slide swiper-slide1" onClick='javascript:window.location.href="<?php echo $v['ad_link']; ?>"' style='background-image:url("<?php echo $v['ad_code']; ?>")'></div>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</section>

			<!--分类-->
			<section class="fication">
				<div class="icon-list">
					<!--<?php if(is_array($cate) || $cate instanceof \think\Collection || $cate instanceof \think\Paginator): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>-->
					<!--<a href="<?php echo url('product/lst',array('id'=>$v['id'])); ?>">-->
						<!--<div class="iconlist"><img class="lazy" src="<?php echo $v['image']; ?>" data-original="<?php echo $v['image']; ?>" /></div>-->
						<!--<p><?php echo $v['mobile_name']; ?></p>-->
					<!--</a>-->
					<!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
					<a href="<?php echo url('product/vegetables',array('cat_id'=>3)); ?>">
						<div class="iconlist"><img class="lazy" src="__PUBLIC__/skin/img/icon/lazyload.gif" data-original="__PUBLIC__/skin/img/icon/ls.png" /></div>
						<p>精品零食</p>
					</a>
					<a href="<?php echo url('product/vegetables',array('cat_id'=>2)); ?>">
						<div class="iconlist"><img class="lazy" src="__PUBLIC__/skin/img/icon/lazyload.gif" data-original="__PUBLIC__/skin/img/icon/rl.png" /></div>
						<p>肉品系列</p>
					</a>
					<a href="<?php echo url('product/package'); ?>">
						<div class="iconlist"><img class="lazy" src="__PUBLIC__/skin/img/icon/lazyload.gif" data-original="__PUBLIC__/skin/img/icon/tc.png" /></div>
						<p>礼盒系列</p>
					</a>
					<a href="<?php echo url('product/vegetables',array('cat_id'=>4)); ?>">
						<div class="iconlist"><img class="lazy" src="__PUBLIC__/skin/img/icon/lazyload.gif" data-original="__PUBLIC__/skin/img/icon/techan.png" /></div>
						<p>特产系列</p>
					</a>
				</div>
			</section>

			<!--产品推荐-->
			<section class="product-box">
				<div class="site-title">
			      <fieldset><legend>精品推荐</legend></fieldset>
			    </div>
				
				<div class="swiper-container swiper2">
					<div class="swiper-wrapper">
						<?php if(is_array($recommends) || $recommends instanceof \think\Collection || $recommends instanceof \think\Paginator): $i = 0; $__LIST__ = $recommends;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<div class="swiper-slide swiper-slide2" onClick='javascript:window.location.href="<?php echo $v['ad_link']; ?>"' style='background-image:url("<?php echo $v['ad_code']; ?>")'></div>
						<?php endforeach; endif; else: echo "" ;endif; ?>
						<!--<div class="swiper-slide swiper-slide2" onClick="javascript:window.location.href='productdetails.html'" style="background-image:url('__PUBLIC__/skin/img/cp2.jpg')"></div>-->
						<!--<div class="swiper-slide swiper-slide2" onClick="javascript:window.location.href='productdetails.html'" style="background-image:url('__PUBLIC__/skin/img/cp3.jpg')"></div>-->
					</div>
					<div class="swiper-pagination swiper-pagination2"></div>
				</div>
			</section>


			
			<!--时蔬-->
			<div class="cp-img">
				<img class="lazy" src="<?php echo $cate[1]['ad_code']; ?>" data-original="<?php echo $cate[1]['ad_code']; ?>">
			</div>
			<section class="product-box-gree">
				<div class="swiper-container swiper3">
					<div class="swiper-wrapper">

						<?php if(is_array($greens) || $greens instanceof \think\Collection || $greens instanceof \think\Paginator): $i = 0; $__LIST__ = $greens;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<div class="swiper-slide">
							<a href="<?php echo url('product/details',array('goods_id'=>$v['goods_id'])); ?>">
								<div class="recom-img">
									<img class="lazy" src="<?php echo $v['original_img']; ?>" data-original="<?php echo $v['original_img']; ?>">
								</div>
								<div class="recom-tit">
									<p class="tit over"><?php echo $v['goods_name']; ?></p>
									<p class="hse"><?php echo $v['weight']; ?>g</p>
									<p class="Theprice">
										<b>￥<?php echo $v['shop_price']; ?></b>
										<button type="button" class="icon-carttwo add-button" data-price="<?php echo $v['shop_price']; ?>" data-proid="<?php echo $v['goods_id']; ?>" data-proname="<?php echo $v['goods_name']; ?>" data-proimg="<?php echo $v['original_img']; ?>"></button>
									</p>
								</div>
							</a>
						</div>
						<?php endforeach; endif; else: echo "" ;endif; ?>

					</div>
				</div>
			</section>
			
			<hr>
			
			<!--套餐-->
			<div class="cp-img">
				<img class="lazy" src="<?php echo $cate[2]['ad_code']; ?>" data-original="<?php echo $cate[2]['ad_code']; ?>">
			</div>
			<section class="product-box-gree">
				<div class="swiper-container swiper3">
					<div class="swiper-wrapper">

						<?php if(is_array($package) || $package instanceof \think\Collection || $package instanceof \think\Paginator): $i = 0; $__LIST__ = $package;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<div class="swiper-slide">
							<a href="<?php echo url('product/details',array('goods_id'=>$v['goods_id'])); ?>">
								<div class="recom-img">
									<img class="lazy" src="<?php echo $v['original_img']; ?>" data-original="<?php echo $v['original_img']; ?>">
								</div>
								<div class="recom-tit">
									<p class="tit over"><?php echo $v['goods_name']; ?></p>
									<p class="hse"><?php echo $v['weight']; ?>g</p>
									<p class="Theprice">
										<b>￥<?php echo $v['shop_price']; ?></b>
										<button type="button" class="icon-carttwo add-button" data-price="<?php echo $v['shop_price']; ?>" data-proid="<?php echo $v['goods_id']; ?>" data-proname="<?php echo $v['goods_name']; ?>" data-proimg="<?php echo $v['original_img']; ?>"></button>
									</p>
								</div>
							</a>
						</div>
						<?php endforeach; endif; else: echo "" ;endif; ?>

					</div>
				</div>
			</section>

			<hr>

			<!--生鲜产品-->
			<div class="cp-img">
				<img class="lazy" src="<?php echo $cate[0]['ad_code']; ?>" data-original="<?php echo $cate[0]['ad_code']; ?>">
			</div>
			<section class="product-box-gree">
				<div class="swiper-container swiper3">
					<div class="swiper-wrapper">
						<?php if(is_array($freshs) || $freshs instanceof \think\Collection || $freshs instanceof \think\Paginator): $i = 0; $__LIST__ = $freshs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<div class="swiper-slide">
							<a href="<?php echo url('product/details',array('goods_id'=>$v['goods_id'])); ?>">
								<div class="recom-img">
									<img class="lazy" src="<?php echo $v['original_img']; ?>" data-original="<?php echo $v['original_img']; ?>">
								</div>
								<div class="recom-tit">
									<p class="tit over"><?php echo $v['goods_name']; ?></p>
									<p class="hse"><?php echo $v['weight']; ?>g</p>
									<p class="Theprice">
										<b>￥<?php echo $v['shop_price']; ?></b>
										<button type="button" class="icon-carttwo add-button" data-price="<?php echo $v['shop_price']; ?>" data-proid="<?php echo $v['goods_id']; ?>" data-proname="<?php echo $v['goods_name']; ?>" data-proimg="<?php echo $v['original_img']; ?>"></button>
									</p>
								</div>
							</a>
						</div>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</div>
				</div>
			</section>


			<!--导航-->
			<nav class="footer">
				<a href="<?php echo url('adopt/index'); ?>">
					<div class="icon">
			            <span class="icon-ziyuan5"></span>
			        </div>
					<p class="tetx">认养</p>
			    </a>
			    <a href="<?php echo url('shop/index'); ?>" class="active">
					<div class="icon">
			            <span class="icon-ziyuan1"></span>
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
			    <a href="<?php echo url('users/index'); ?>">
					<div class="icon">
			            <span class="icon-ziyuan9"></span>
			        </div>
					<p class="tetx">我的</p>
			    </a>
			</nav>

		</div>

		<!--加入购物车-->
		<section class="cd-cart-container <?php if(count($carts) <= 0){ echo 'empty';}?>">
			<a href="javascript:;" class="cd-cart-trigger">
				<ul class="count">
					<li><?php echo count($carts);?></li>
					<li>0</li>
				</ul>
			</a>
			<div class="cd-cart">
				<div class="wrapper">
					<header>
						<h2>购物车</h2>
					</header>
					<div class="body">
						<ul>
							<?php $money = 0; if(is_array($carts) || $carts instanceof \think\Collection || $carts instanceof \think\Paginator): $i = 0; $__LIST__ = $carts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;
							$money += $v['goods_num'] * $v['shop_price'];
							?>
							<li class="product">
								<div class="product-image">
									<img src="<?php echo $v['original_img']; ?>" alt="placeholder">
								</div>
								<div class="product-details">
									<h3><?php echo $v['goods_name']; ?></h3>
									<span class="price">￥<?php echo $v['shop_price']; ?></span>
									<div class="actions">
										<div class="quantity">
											<label for="cd-product-42">份数</label>
											<span class="select">x<i id="cd-product-42"><?php echo $v['goods_num']; ?></i></span>
										</div>
									</div>
								</div>
							</li>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
					<footer><a class="checkout" href="<?php echo url('Cart/index'); ?>"><em>去结算￥<span><?php echo $money; ?></span></em></a></footer>
				</div>
			</div>
		</section>
		
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
		<script src="__PUBLIC__/skin/js/shopping.js"></script>
		<script src="__PUBLIC__/skin/js/common.js"></script>
		<script>
			$(function() {
				//banner
				$('.swiper-slide1').css('height', $('.swiper-slide1').width()  * 0.46);
				var swiper1 = new Swiper('.swiper1', {
				    effect: 'coverflow',
					centeredSlides: true,
					loop: true,
					autoplay: true,
					speed: 1000,
					slidesPerView: 'auto',
					autoplayDisableOnInteraction: false,
					coverflow: {
						rotate: 0,
						stretch: -55,
						depth: 750,
						modifier: 1,
						slideShadows: false
					}
				   
				});
				
				//精品推荐
				$('.swiper-slide2').css('height', $('.swiper-slide2').width()  * 1.1);
				var swiper2 = new Swiper('.swiper2', {
					effect : 'coverflow',
					loop: true,
					autoplay: 3000,
					speed: 1000,
				    slidesPerView: 'auto',
				    centeredSlides: true,
				    coverflowEffect: {
					    rotate: 0,
					    stretch: -55,
					    depth: 750,
					    modifier: 1,
					    slideShadows : false
				    },
				    pagination: {
				        el: '.swiper-pagination2',
				    },
				});

			    //产品滑动
				var swiper3 = new Swiper('.swiper3', {
					slidesPerView: 3,
					spaceBetween : 20,
				});
			    
			    //预加载
				$("img.lazy").lazyload({
					effect: "fadeIn"
				});
			});
			
			//loading
			var $preloader=$('.mask-dialog'),
			$spinner=$preloader.find('.spinner');
	        $spinner.fadeOut();
			$preloader.delay(2000).fadeOut('slow');
		</script>
	</body>

</html>