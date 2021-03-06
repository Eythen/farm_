<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/book/orderlist.html";i:1516084570;}*/ ?>
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
		<title>订桌清单</title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/mescroll.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/book.css" rel="stylesheet">

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>

		<header class="header">
			<a class="icon-jiantou-left" href="<?php echo url('users/index'); ?>"></a>
			<div class="header-title">订桌清单</div>
		</header>

		<div class="red-nav">
			<a class="active" i="0">已预订</a>
			<a i="1">已过期</a>
			<a i="2">退订中</a>
			<a i="3">已完成</a>
		</div>
		
		<div class="redorder">
			
			<!--已预订-->
			<div id="mescroll0" class="mescroll">
				<ul id="dataList0" class="orderlist">
					<!--<li>
						<div class="orderlist-hd">
							<span>单号：<em>2132165457654545</em></span>
							<span>已预订</span>
						</div>
						<div class="orderlist-box">
							<div class="location">位置：<span>1厅1A号</span></div>
							<div class="Table-details">
								<div class="details-list">
									<p>14</p>
									<p>人数</p>
								</div>
								<div class="details-list">
									<p>2017-12-15</p>
									<p>用餐时间</p>
								</div>
								<div class="details-list">
									<p>400</p>
									<p>订单总价</p>
								</div>
							</div>
						</div>
						<div class="order-price">
							<div class="order-price-left">已付订金：<span><small>￥</small><i>800</i></span></div>
							<div class="order-price-left">未付余额：<span><small>￥</small><i>300</i></span></div>
						</div>
						<div class="order-btn">
							<a href="javascript:;" class="orderlistbtn">取消订单</a>
							<a href="javascript:;" class="orderlistbtn red">付款</a>
							<a href="javascript:;" class="orderlistbtn">查看订单</a>
						</div>
					</li>-->
				</ul>
			</div>
			
			<!--已过期-->
			<div id="mescroll1" class="mescroll hide">
				<ul id="dataList1" class="orderlist">
				</ul>
			</div>
			
			<!--退订中-->
			<div id="mescroll2" class="mescroll hide">
				<ul id="dataList2" class="orderlist">
				</ul>
			</div>
			
			<!--已完成-->
			<div id="mescroll3" class="mescroll hide">
				<ul id="dataList3" class="orderlist">
				</ul>
			</div>
			
		</div>
		
		

		<!-- js -->
		<script src="__PUBLIC__/skin/js/jquery.min.js"></script>
		<script src="__PUBLIC__/skin/js/mescroll.js"></script>
		<script src="__PUBLIC__/skin/js/fastclick.js"></script>
		<script src="__PUBLIC__/skin/js/jquery-weui.js"></script>
		<script>
			$(function() {
				//消除延迟
				FastClick.attach(document.body);

				//提交支付剩余时间
				function countTime() {
					$('.redorder .hour').each(function(){  
			            //获取当前时间  
			            var date = new Date();  
			            var now = date.getTime();  
			            //设置截止时间  
			            var str = $(this).data('endtime');
			            //var str = "2017/5/17 00:00:00";
			            var endDate = new Date(str); 
			            var end = endDate.getTime();  
			            
			            //时间差  
			            var leftTime = end-now; 
			            //定义变量 d,h,m,s保存倒计时的时间  
			            var d,h,m,s;  
			            if (leftTime>=0) {  
			                //d = Math.floor(leftTime/1000/60/60/24);  
			                //h = Math.floor(leftTime/1000/60/60%24);  
			                m = Math.floor(leftTime/1000/60%60);  
			                s = Math.floor(leftTime/1000%60); 
			                if(s<10){
			                	s = '0'+s;
			                }
			                $(this).html(m+':'+s);                    
			            }
			            else{
			            	$(this).closest('.colocyse').html('订单已关闭支付');
			            } 
		  			})
		        } 
		        //每秒更新 倒计分秒
		        setInterval(countTime,1000);
				

				var curNavIndex = 0; 
				var mescrollArr = new Array(4); //4个菜单所对应的4个mescroll对象
				//初始化首页
				mescrollArr[0] = initMescroll("mescroll0", "dataList0");

				/*初始化菜单*/
				$(".red-nav a").click(function() {
					var i = Number($(this).attr("i"));
					if(curNavIndex != i) {
						//更改列表条件
						$(".red-nav .active").removeClass("active");
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
							noMoreSize: 0, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
							empty: {
								icon: "__PUBLIC__/skin/img/icon/emptyfile.png", //图标,默认null
								tip: "暂无相关数据", //提示

							},
							clearEmptyId: clearEmptyId //相当于同时设置了clearId和empty.warpId; 简化写法;默认null
						}
					});
					return mescroll;
				}

				/*联网加载列表数据  page = {num:1, size:10}; num:当前页 从1开始, size:每页数据条数 */
				function getListData(page) {
					//联网加载数据
					console.log("curNavIndex=" + curNavIndex + ", page.num=" + page.num);
					getListDataFromNet(curNavIndex, page.num, page.size, function(data) {
						//联网成功的回调,隐藏下拉刷新和上拉加载的状态;
						console.log("data.length=" + data.length);
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
						
						var str = '<div class="orderlist-hd">';
							str += '<span>单号：<em>'+ pd.pdordernumber +'</em></span>';
							if(pd.pdreserved == '已预订'){
								str += '<span>'+ pd.pdreserved +'</span>';
							} else if(pd.pdreserved == '已过期'){
								str += '<span>'+ pd.pdreserved +'</span>';
							} else if(pd.pdreserved == '退订中'){
								str += '<span>'+ pd.pdreserved +'</span>';
							} else if(pd.pdreserved == '已完成'){
								str += '<span>'+ pd.pdreserved +'</span>';
							}
							str += '</div>';
							str += '<div class="orderlist-box">';
							str += '<div class="location">位置：';
							str += '<span>'+ pd.pdposition +'</span>';
							if(pd.pdreserved == '已预订'){
								str += '<span class="fr colocyse">';
	                            str += '<span class="mtla">距离订单关闭</span>';
	                            str += '<span class="hour" data-endtime="'+ pd.end_time +'">' + pd.spare_time+ '</span>';
	                            str += '</span>';
							}
							str += '</div>';
							str += '<div class="Table-details">';
							str += '<div class="details-list">';
							str += '<p>'+ pd.pdpeople +'</p>';
							str += '<p>人数</p>';
							str += '</div>';
							str += '<div class="details-list">';
							str += '<p>'+ pd.pddiningtime +'</p>';
							str += '<p>用餐时间</p>';
							str += '</div>';
							str += '<div class="details-list">';
							str += '<p>'+ pd.pdtotalprice +'</p>';
							str += '<p>订单总价</p>';
							str += '</div>';
							str += '</div>';
							str += '</div>';
							str += '<div class="order-price">';
							if(pd.pay_status){
								if(pd.pay_status == 2){
									str += '<div class="order-price-left">已付订金：<span><small>￥</small><i>'+ pd.amount20 +'</i></span></div>';
									str += '<div class="order-price-left">未付余额：<span><small>￥</small><i>'+ pd.amount80 +'</i></span></div>';
								}
								else{
									str += '<div class="order-price-left">已付：<span><small>￥</small><i>'+ pd.order_amount +'</i></span></div>';
									str += '<div class="order-price-left">未付余额：<span><small>￥</small><i>0.00</i></span></div>';
								}
							}
							else{
								str += '<div class="order-price-left">已付订金：<span><small>￥</small><i>0.00</i></span></div>';
								str += '<div class="order-price-left">未付余额：<span><small>￥</small><i>'+ pd.order_amount +'</i></span></div>';
							}
							str += '</div>';
							str += '<div class="order-btn">';
							if(pd.pdreserved == '已预订'){
								str += '<a href="javascript:;" class="orderlistbtn cancel" data-id="'+ pd.order_id +'"">取消订单</a>';
								if(pd.can_pay){
									if(pd.pay_status == 2){
										str += '<a href="' + pd.pdurl2 + '" class="orderlistbtn red">支付余额</a>';
									}
									else if(pd.pay_status == 1){

									}
									else{
										str += '<a href="' + pd.pdurl + '" class="orderlistbtn red">付款</a>';
									}
								}
								str += '<a href="' + pd.pdlink + '" class="orderlistbtn">查看订单</a>';
						   } else if(pd.pdreserved == '已过期'){
								str += '<a href="' + pd.pdlinktwo + '" class="orderlistbtn">查看订单</a>';
							} else if(pd.pdreserved == '退订中'){
								str += '<a href="' + pd.pdlinkthree + '" class="orderlistbtn">退订中</a>';
							} else if(pd.pdreserved == '已完成'){
								str += '<a href="' + pd.pdlinkfour + '" class="orderlistbtn">查看订单</a>';
							}
							str += '</div>';

						var liDom = document.createElement("li");
						liDom.innerHTML = str;
						listDom.appendChild(liDom);
					}
				}

				/*联网加载列表数据*/
				function getListDataFromNet(curNavIndex, pageNum, pageSize, successCallback, errorCallback) {
					//延时一秒,模拟联网
					//setTimeout(function() {
			          	$.ajax({
			                type: 'GET',
			                url: '<?php echo url("orderList"); ?>?curNavIndex='+curNavIndex+'&num='+pageNum+'&size='+pageSize,
			                dataType: 'json',
			                success: function(data){
								//var data = orderlistjson; // 模拟数据
								var data = data; // 模拟数据
								var listData = [];
					
								if(curNavIndex == 0) {
									//已预订
									for(var i = 0; i < data.length; i++) {
										if(data[i].pdreserved.indexOf("已预订") != -1) {
											listData.push(data[i]);
										}
									}
					
								} else if(curNavIndex == 1) {
									//已过期
									for(var i = 0; i < data.length; i++) {
										if(data[i].pdreserved.indexOf("已过期") != -1) {
											listData.push(data[i]);
										}
									}
					
								} else if(curNavIndex == 2) {
									//退订中
									for(var i = 0; i < data.length; i++) {
										if(data[i].pdreserved.indexOf("退订中") != -1) {
											listData.push(data[i]);
										}
									}
					
								} else if(curNavIndex == 3) {
									//已完成
									for(var i = 0; i < data.length; i++) {
										if(data[i].pdreserved.indexOf("已完成") != -1) {
											listData.push(data[i]);
										}
									}
					
								}
					
								//回调
								successCallback(listData);
								popu();
						  	},
						    error: errorCallback
						});
					//}, 500)
				}

				//禁止PC浏览器拖拽图片,避免与下拉刷新冲突;如果仅在移动端使用,可删除此代码
				document.ondragstart = function() {
					return false;
				}
			});

			//弹窗
			function  popu(id){
				$('.cancel').click(function(event) {
					var order_id = $(this).data('id');
					$.confirm("您确定取消订单吗？", "取消订单", function() {
						$.showLoading();
						$.ajax({
	                        url: "<?php echo url('cancel'); ?>",
	                        type: 'POST',
	                        dataType: 'json',
	                        data: {order_id: order_id},
	                        success: function(data){
	                            if( data.code ){
	                                window.location.href='<?php echo url("orderList"); ?>';
	                            }else{
                            		$.hideLoading();
	                                $.toast("取消失败！", "cancel");
	                            }
	                        }
	                    })
					}, function() {
						$.toast("您已取消订单", "text");
					});
				});
			}
		</script>
		
		<!--订单数据加载-->
		<!-- <script src="__PUBLIC__/skin/js/orderlist.js"></script>
		<script src="__PUBLIC__/skin/js/orderlistjson.js"></script> -->
		<script>
			
		</script>

	</body>

</html>