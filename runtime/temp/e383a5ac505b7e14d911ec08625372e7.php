<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/book/table.html";i:1516084570;}*/ ?>
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
		<title>预约订桌</title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/book.css" rel="stylesheet">
		<style>
			body {
				background: #fff;
			}
		</style>

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>

		<header class="header">
			<a class="icon-jiantou-left" href="<?php echo url('index'); ?>"></a>
			<div class="header-title">预约订桌</div>
		</header>

		<div class="table-pt">

			<div class="table-txte">
				<span class="icon-baocu"></span>
				<span>选定位置后只保留<b>60分钟</b>，请准时到达</span>
			</div>

			<div class="seat_area">
				<div id="seat_area"></div>

				<div class="table-box">
					<ul class="List">
						<li>
							<div class="List-box optional available"></div><span>可选</span></li>
						<li>
							<div class="List-box selected unavailable"></div><span>已选</span></li>
						<li>
							<div class="List-box notoptional available"></div><span>不可选</span></li>
					</ul>
				</div>
			</div>

			<div class="booking-details">
				<h3>已选台位<span id="tickets_num">0</span>张</h3>
				<ul id="seats_selected"></ul>
				<div class="right">
					<input type="button" class="table-btn disable" value="立即订桌"/>
				</div>
			</div>
		</div>

		<!-- js -->
		<script src="__PUBLIC__/skin/js/jquery.min.js"></script>
		<script src="__PUBLIC__/skin/js/fastclick.js"></script>
		<script src="__PUBLIC__/skin/js/jquery-weui.js"></script>
		<script>
			$(function() {
				//消除延迟
				FastClick.attach(document.body);
			});
		</script>
		<script src="__PUBLIC__/skin/js/jquery.seat-charts.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {

				var $cart = $('#seats_selected'),
					$tickets_num = $('#tickets_num'),
					sc = $('#seat_area').seatCharts({
						map: [ //座位结构图 f 代表一层小包间;  e 代表一层大包间; g 代表一层; 下划线 "_" 代表过道
							'____ff',
							'e_____',
							'______',
							'gg_ggg',
							'gg_ggg',
						],
						seats: {
							e: {
								Number: 12,
								/*category: '一层大包间'*/
							},
							f: {
								Number: 8,
								/*category: '一层小包间'*/
							},
							g: {
								Number: 6,
								/*category: '一层'*/
							}
						},
						naming: { //设置行列等信息
							top: true,
							columns: ['A', 'B', '', 'C', 'D', 'F'],
							rows: ['1', '2', '', '3', '4'],
							getLabel: function(character, row, column) {
								return row + column;
							}
						},

						click: function() {
							if(this.status() == 'available') { //若为可选状态
								var table = this.settings.id,
									label = this.settings.label,
									sc2 = $('#seat_area'),
									number = this.data().Number;
									//flag = 0;
								$.ajax({
			                        url: "<?php echo url('tableStatus'); ?>",
			                        type: 'POST',
			                        dataType: 'json',
			                        data: {table: table},
			                        success: function(data){
			                            if( data.code ){
			                                //flag = 1;
											$('<li>1厅' + label + '号<br/>' + number + '人位</li>')
												.attr('id', 'cart-item-' + table)
												.data('seatId', table)
												.appendTo($cart); //追加

											sc.get([table]).status('selected');
											$tickets_num.text(sc2.find('selected').length + 1); //统计数量
											jss();
			                            }else{
			                            	sc.get([table]).status('unavailable');
			                                $.toast(data.msg, "cancel");
			                                return false;

			                            }
			                        }
			                    })
							} else if(this.status() == 'selected') { //选中状态
								$tickets_num.text(sc.find('selected').length - 1);
								$('#cart-item-' + this.settings.id).remove(); //删除已选
								jss();
								return 'available';
							} else if(this.status() == 'unavailable') { //不可选
								return 'unavailable';
							} else {
								return this.style();
							}
						},
					});
				<?php if(!(empty($booked) || (($booked instanceof \think\Collection || $booked instanceof \think\Paginator ) && $booked->isEmpty()))): if(is_array($booked) || $booked instanceof \think\Collection || $booked instanceof \think\Paginator): $i = 0; $__LIST__ = $booked;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>	
				sc.get(["<?php echo $vo; ?>"]).status('unavailable'); //不可选
				<?php endforeach; endif; else: echo "" ;endif; endif; ?>
			});

			function getMousePosition(event) { //获取鼠标横坐标和纵坐标
				var point = {
					x: 0,
					y: 0
				};
				if(typeof window.pageYOffset != 'undefined') {
					point.x = window.pageXOffset;
					point.y = window.pageYOffset;
				} else if(typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat') {
					point.x = document.documentElement.scrollLeft;
					point.y = document.documentElement.scrollTop;
				} else if(typeof document.body != 'undefined') {
					point.x = document.body.scrollLeft;
					point.y = document.body.scrollTop;
				}
				point.x += event.clientX;
				point.y += event.clientY;
				return point; // 返回坐标数组x,y
			}

			function jss() {
				var tickets_num = $("#tickets_num").html();
				if(tickets_num > 0) {
					$(".right").find("input").removeClass("disable",false);
				} else {
					$(".right").find("input").addClass("disable",true);
				}
			};

			$('.table-btn').on('click', function(){
				if($(this).hasClass('disable')){
					console.log('has .disable');
					return false;
				}
				else{
					var r, re; // 声明变量。 
					var s = $('#seats_selected').html(); 
					re = /<li id="cart-item-(.*?)">/gi; // 创建正则表达式模式。 
					r = s.match(re); // 尝试去匹配搜索字符串
					for (var i = 0; i < r.length; i++) {
						r[i] = r[i].replace(re, "$1");
					}
					r = r.join(',');
					$.showLoading();
					$.ajax({
                        url: "<?php echo url('table'); ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: {table: r},
                        success: function(data){
                            if( data.code ){
                                window.location.href='<?php echo url("ordering"); ?>';
                            }else{
                            	$.hideLoading();
                                $.toast("提交失败！", "cancel");
                            }
                        }
                    })
				}
				//console.log('has .disable2');
			})

		</script>

	</body>

</html>