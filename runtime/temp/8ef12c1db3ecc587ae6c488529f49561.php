<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/users/red.html";i:1516084569;}*/ ?>
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
		<title><?php echo $type_name; ?></title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/mescroll.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<style type="text/css">
			.red-list {
			    height: auto;
			}
		</style>

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>

		<header class="header">
			<a class="icon-jiantou-left" href="javascript:history.go(-1);"></a>
			<div class="header-title"><?php echo $type_name; ?></div>
		</header>

		<div class="myorder-nav">
			<a class="active" i="0">可用</a>
			<a i="1">已使用</a>
			<a i="2">已过期</a>
		</div>
		<div class="myorder">
			
			<!--可用-->
			<div id="mescroll0" class="mescroll">
				<ul id="dataList0" class="red-list">
				</ul>
			</div>
			
			<!--已使用-->
			<div id="mescroll1" class="mescroll hide">
				<ul id="dataList1" class="red-list">
				</ul>
			</div>
			
			<!--已过期-->
			<div id="mescroll2" class="mescroll hide">
				<ul id="dataList2" class="red-list">
				</ul>
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
			
				var curNavIndex = 0; //全部0; 待付款1; 待发货2; 待收货3; 已完成4;
				var mescrollArr = new Array(3); //3个菜单所对应的3个mescroll对象
				//初始化首页
				mescrollArr[0] = initMescroll("mescroll0", "dataList0");

				/*初始化菜单*/
				$(".myorder-nav a").click(function() {
					var i = Number($(this).attr("i"));
					if(curNavIndex != i) {
						//更改列表条件
						$(".myorder-nav .active").removeClass("active");
						$(this).addClass("active");
						//隐藏当前列表
						$("#mescroll" + curNavIndex).hide();
						//显示对应的列表
						curNavIndex = i;
						$("#mescroll" + curNavIndex).show();
						//取出菜单所对应的mescroll对象,如果未初始化则初始化
						if(mescrollArr[i] == null) mescrollArr[i] = initMescroll("mescroll" + i, "dataList" + i);
					}
				})

				/*创建MeScroll对象*/
				function initMescroll(mescrollId, clearEmptyId) {
					//创建MeScroll对象,内部已默认开启下拉刷新,自动执行up.callback,刷新列表数据;
					var mescroll = new MeScroll(mescrollId, {
						//上拉加载的配置项
						up: {
							callback: getListData, //上拉回调,此处可简写; 相当于 callback: function (page) { getListData(page); }
							noMoreSize: 4, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
							empty: {
								icon: "__PUBLIC__/skin/img/icon/emptyfile.png", //图标,默认null
								tip: "暂无相关数据~", //提示
							},
							toTop:{ //配置回到顶部按钮
								src : "__PUBLIC__/skin/img/mescroll-totop.png", //默认滚动到1000px显示,可配置offset修改
								offset : 10
							},
							clearEmptyId: clearEmptyId //相当于同时设置了clearId和empty.warpId; 简化写法;默认null
						}
					});
					return mescroll;
				}

				/*联网加载列表数据  page = {num:1, size:10}; num:当前页 从1开始, size:每页数据条数 */
				function getListData(page) {
					//联网加载数据
					/*console.log("curNavIndex=" + curNavIndex + ", page.num=" + page.num);*/
					getListDataFromNet(curNavIndex, page.num, page.size, function(data) {
						//联网成功的回调,隐藏下拉刷新和上拉加载的状态;
						mescrollArr[curNavIndex].endSuccess(data.length); //传参:数据的总数; mescroll会自动判断列表如果无任何数据,则提示空;列表无下一页数据,则提示无更多数据;
						//设置列表数据
						setListData(data);
					}, function() {
						//联网失败的回调,隐藏下拉刷新和上拉加载的状态;
						mescrollArr[curNavIndex].endErr();
					});
				}

				/*设置列表数据*/
				function setListData(data) {
					var listDom = document.getElementById("dataList" + curNavIndex);
					for(var i = 0; i < data.length; i++) {
						var pd = data[i];

						var str =   '<div class="group">'+
										'<div class="hb">'+
											'<img src="' + pd.img + '"/>';
											if(pd.pdavailable == '可用'){
												str += '<div class="box coupon-hb">'
											}else if(pd.pdavailable == '已使用'){
												str += '<div class="box coupon-gree">'
											}else if(pd.pdavailable == '已过期'){
												str += '<div class="box coupon-gree">'
											}
											if(pd.type==7){
												str +='<h1>' + pd.money + '<small>折</small></h1>';
											}
											else{
												str +='<h1><small>￥</small>' + pd.money + '</h1>';
											}
												str += '<p>'+ pd.condition +'</p>'+
											'</div>'+
											'<div class="right">'+
									            '<p>'+ pd.type_name +'</p>'+
									            '<p>'+ pd.time +'</p>'+
											'</div>'+
										'</div>'+
									'</div>';
						var liDom = document.createElement("li");
						liDom.innerHTML = str;
						listDom.appendChild(liDom);
					}
				}

				/*联网加载列表数据*/
				function getListDataFromNet(curNavIndex, pageNum, pageSize, successCallback, errorCallback) {
					$.ajax({
					    type: 'GET',
					    url: '<?php echo url("coupon"); ?>?type=<?php echo $type; ?>&curNavIndex='+curNavIndex+'&num='+pageNum+'&size='+pageSize,
					    dataType: 'json',
					    success: function(data){
							//var data = red; // 模拟数据
							if(!data.total){
								$('.upwarp-tip').text('暂没数据！');
								$('.mescroll-rotate').remove();
								return false;
							}
							var data = data.rows;
							var listData = [];
							for(var i = 0; i < data.length; i++) {
								listData.push(data[i]);
							}

							//curNavIndex 全部0; 待付款1; 待发货2; 待收货3; 已完成4;
							/*if(curNavIndex == 0) {
								//可用
								for(var i = 0; i < data.length; i++) {
									if(data[i].pdavailable.indexOf("可用") != -1) {
										listData.push(data[i]);
									}
								}
							} else if(curNavIndex == 1) {
								//已使用
								for(var i = 0; i < data.length; i++) {
									if(data[i].pdavailable.indexOf("已使用") != -1) {
										listData.push(data[i]);
									}
								}
							} else if(curNavIndex == 2) {
								//已过期
								for(var i = 0; i < data.length; i++) {
									if(data[i].pdavailable.indexOf("已过期") != -1) {
										listData.push(data[i]);
									}
								}
							} */
							//回调
							successCallback(listData);
						},
					    error: errorCallback
					});
					//延时一秒,模拟联网
					/*setTimeout(function() {
					}, 1000)*/
				}

				//禁止PC浏览器拖拽图片,避免与下拉刷新冲突;如果仅在移动端使用,可删除此代码
				document.ondragstart = function() {
					return false;
				}
			});
		</script>
		<script src="__PUBLIC__/skin/js/jquery-weui.js"></script>
		
		<!-- <script src="__PUBLIC__/skin/js/red-list.js"></script>
		<script src="__PUBLIC__/skin/js/red.js"></script> -->


	</body>

</html>