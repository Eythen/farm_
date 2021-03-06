<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:80:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/book/ordering.html";i:1516084570;}*/ ?>
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
		<title>预约订餐</title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/mescroll.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/book.css" rel="stylesheet">
		<style>
			.mescroll-upwarp {
			    text-align: center;
			    visibility: hidden;
			    width: 75%;
			    margin: 0 auto;
			     
			}
			.mescroll-downwarp .downwarp-content {
			    position: absolute;
			    left: 0px;
			    bottom: 0;
			    width: 75%;
			    padding: 10px 0;
			    margin: 0 auto;
			    padding-left: 188px;
			}
			.upwarp-nodata {
			    width: 75%;
			    margin: 0 auto;
			}
			.weui-popup__overlay{
				opacity: 1;
				 -webkit-filter:blur(3px)
			}

		</style>

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>
		
		<!--banner-->
		<div class="ordering-banner">
			<img src="__PUBLIC__/skin/img/200.jpg" />
		</div>
        
        <!--选项卡-->
        <ul class="menu-left scrollbar-none" id="sidebar">
	        <!-- <li class="active">套餐A</li>
	        <li>套餐B</li>
	        <li>套餐C</li>
	        <li>套餐D</li>
	        <li>套餐E</li>
	         -->
	        <?php if(is_array($combo) || $combo instanceof \think\Collection || $combo instanceof \think\Paginator): $i = 0; $__LIST__ = $combo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>

				<li<?php if($i == '1'): ?> class="active"<?php endif; ?>><?php echo $vo['goods_name']; ?></li>
	        
	        <?php endforeach; endif; else: echo "" ;endif; ?>
	        <li>更多单品</li>
	    </ul>
	    
	    <div class="menu-right-pd">
		    <!--套餐A-->
				<?php if(is_array($combo) || $combo instanceof \think\Collection || $combo instanceof \think\Paginator): $i = 0; $__LIST__ = $combo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
				<ul class="menu-right content pd" id="mobilelist"<?php if($i != '1'): ?> style="display: none;"<?php endif; ?>>
				<?php if(is_array($vo['combo_goods']) || $vo['combo_goods'] instanceof \think\Collection || $vo['combo_goods'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['combo_goods'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
					<li>
			    		<div class="prolist">
			    			<div class="menu-img"><img src="<?php echo $v['original_img']; ?>" /></div>
			    			<div class="menu-txt">
			    							<h4><?php echo $v['goods_name']; ?></h4>
			    							<p><?php echo $v['goods_remark']; ?></p>
			    							<h2><small>￥</small><span><?php echo $v['shop_price']; ?></span></h2>
			    						</div>
			    		</div>
			    	</li>
				<?php endforeach; endif; else: echo "" ;endif; ?>
					<div class="shopPrice">总计：<span class="price"><small>￥</small><?php echo $vo['shop_price']; ?></span>
    					<div class="btn">
    						<button class="minus"><strong class="btn1"></strong></button>
    						<i class="combo" data-id="<?php echo $vo['id']; ?>">0</i>
    						<button class="add"><strong class="btn2"></strong></button>
    						<i class="price"><?php echo $vo['shop_price']; ?></i>
    					</div>
    				</div>
    				<div id="mobileprice"></div>
				</ul>
				<?php endforeach; endif; else: echo "" ;endif; ?>
		    
		    
		    <!--更多单品-->
		    <div class="menu-pd content" style="display: none;">
			    <div id="mescroll" class="mescroll">
				    <ul id="dataList" class="menu-right menu-sefwef">
				    	<!--<li>
				    		<div class="prolist">
				    			<div class="menu-img"><img src="../img/pic.jpg" /></div>
				    			<div class="menu-txt">
									<h4>蓝之蓝蓝色瓶装经典Q7浓香型白酒500ml52度高端纯粮食酒2瓶装包邮包邮</h4>
									<p>蓝之蓝蓝色瓶装经典Q7浓香型白酒500ml52度高端纯粮食酒2瓶装包邮包邮</p>
									<div class="ordering-price">
										<h2><small>￥</small><span>25.00</span></h2>
										<div class="btn">
											<button class="minus"><strong class="btn1"></strong></button>
											<i>0</i>
											<button class="add"><strong class="btn2"></strong></button>
											<i class="price">25</i>
										</div>
									</div>
								</div>
				    		</div>
				    	</li>-->
				    </ul>
				</div>
			</div>
			
			<!--结算导航-->
			<div class="ordering-nav">
				<div class="ordering-left">
					<div id="cartN">
						<a href="cart.html">
							<span class="icon-carttwo"></span>
							<span id="totalcountshow">0</span>
						</a>
						<span class="totalpriceshow">
							<span class="gray">合计</span>
							<small>￥</small>
							<span id="totalpriceshow">0.00</span>
						</span>
					</div>
				</div>
				<div class="ordering-right">
					<a id="btnselect" class="xhlbtn disable" href="javascript:void(0);">结算</a>
				</div>
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
			});

	//数据加载
	var mescroll = new MeScroll("mescroll", {
		up: {
			clearEmptyId: "dataList",
			callback: getListData,
		}
	});
	function getListData(page) {
		//console.log("page.num==" + page.num); //page = {num:1, size:10};每页数据条数
		getListDataFromNet(page.num, page.size, function(data) {
			mescroll.endSuccess(data.length);
			setListData(data);
		}, function() {
			mescroll.endErr();
		});
	}
	function setListData(data) {
		var listDom = document.getElementById("dataList");
		for(var i = 0; i < data.length; i++) {
			var pd = data[i];
			
			var str = '<div class="prolist">';
			    str += '<div class="menu-img"><img src="'+ pd.original_img +'" /></div>';
			    str += '<div class="menu-txt">';
				str += '<h4>'+ pd.goods_name +'</h4>';
				str += '<p>'+ pd.goods_remark +'</p>';
				str += '<div class="ordering-price">';
				str += '<h2><smll>￥</smll><span>'+ pd.shop_price +'</span></h2>';
				str += '<div class="btn">';
				str += '<button class="minus"><strong class="btn1"></strong></button>';
				str += '<i class="goods" data-id="'+ pd.goods_id +'">0</i>';
				str += '<button class="add"><strong class="btn2"></strong></button>';
				str += '<i class="price">'+ pd.shop_price +'</i>';
				str += '</div>';
				str += '</div>';
				str += '</div>';
			    str += '</div>';

			var liDom = document.createElement("li");
			liDom.innerHTML = str;
			listDom.appendChild(liDom);
		}
	}
	var dataTag = 1;
	$(".btn-change").click(function() {
		if(1) {
			dataTag = 1;
			$(this).html("已模拟后台修改信息 <b>1</b> , 请下拉刷新");
		}
	});
	function getListDataFromNet(pageNum, pageSize, successCallback, errorCallback) {
		setTimeout(function() {
			$.ajax({
				type: 'GET',
				url: '<?php echo url("goodslist"); ?>?num=' + pageNum + "&size=" + pageSize,
				dataType: 'json',
				success: function(data) {
					//console.log(data);
					//var data = orderingjson; // 模拟数据
					var data = data; // 模拟数据
		
					var listData = data;
					//var listData = [];
					/*for(var i = (pageNum - 1) * pageSize; i < pageNum * pageSize; i++) {
						if(i == data.length) break;
						listData.push(data[i]);
					}*/
					successCallback(listData);
					jan()
			    },
				error: errorCallback
			});
		}, 1000)
	}

	//禁止PC浏览器拖拽图片,避免与下拉刷新冲突;如果仅在移动端使用,可删除此代码
	document.ondragstart = function() {
		return false;
	}

    //选项卡
	$('#sidebar li').click(function() {
		$(this).addClass('active').siblings('li').removeClass('active');
		var index = $(this).index();
		$('.content').eq(index).show().siblings('.content').hide();
	})

    function jan() {
	    //加
		$(".add").click(function() {
			$(this).prevAll().css("display", "inline-block");
			var n = $(this).prev().text();
			var num = parseInt(n) + 1;
			if(num == 0) {
				return;
			}
			$(this).prev().text(num);
			var danjia = $(this).next().text(); //获取单价  
			var a = $("#totalpriceshow").html(); //获取当前所选总价  
			$("#totalpriceshow").html((a * 1 + danjia * 1).toFixed(2)); //计算当前所选总价  
			var nm = $("#totalcountshow").html(); //获取数量  
			$("#totalcountshow").html(nm * 1 + 1);
			jss(); //改变按钮样式
		});
		//减的 
		$(".minus").click(function() {
			var n = $(this).next().text();
			var num = parseInt(n) - 1;
			$(this).next().text(num); //减1  
			var danjia = $(this).nextAll(".price").text(); //获取单价  
			var a = $("#totalpriceshow").html(); //获取当前所选总价  
			$("#totalpriceshow").html((a * 1 - danjia * 1).toFixed(2)); //计算当前所选总价  
			var nm = $("#totalcountshow").html(); //获取数量  
			$("#totalcountshow").html(nm * 1 - 1);
			//如果数量小于或等于0则隐藏减号和数量  
			if(num <= 0) {
				$(this).next().css("display", "none");
				$(this).css("display", "none");
				jss(); //改变按钮样式  
				return
			}
		});
    }
	//按钮颜色改变
	function jss() {
		var totalcountshow = $("#totalcountshow").html();
		if(totalcountshow > 0) {
			$(".ordering-right").find("a").removeClass("disable",false);
		} else {
			$(".ordering-right").find("a").addClass("disable",true);
		}
	};


	$('#btnselect').on('click', function(){
		if($(this).hasClass('disable')){
			return false;
		}
		else{
			var combo = new Array(),
				goods = new Array(),
				no = 0,
				no2 = 0;
			$('.btn i').each(function(){
				if( ($(this).data('id')>0) && ($(this).text()>0)){
					//console.log($(this).text());
					if($(this).hasClass('combo')){
						combo[no] = new Array();
						combo[no]['id'] = $(this).data('id');
						combo[no]['num'] = $(this).text();
						no++;
					}
					else{
						goods[no2] = new Array();
						goods[no2]['id'] = $(this).data('id');
						goods[no2]['num'] = $(this).text();
						no2++
					}
				}
			})
			var combo_str = '',
				goods_str = '';

			//套餐
			if(combo){
				for (var i = 0; i < combo.length; i++) {
					//console.log(combo[i]);
					combo_str += '{"id":' + combo[i]['id']+', "num":' + combo[i]['num'] + '},';
				}
				combo_str = combo_str.substring(0, (combo_str.length-1));
				combo_str = '['+combo_str + ']';
			}
			//单品
			if(goods){
				for (var i = 0; i < goods.length; i++) {
					goods_str += '{"id":' + goods[i]['id']+', "num":' + goods[i]['num'] + '},';
				}
				goods_str = goods_str.substring(0, (goods_str.length-1));
				goods_str = '['+goods_str + ']';
			}

			var data = {goods:goods_str, combo:combo_str};

			$.showLoading();
			$.ajax({
                url: "<?php echo url('ordering'); ?>",
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(data){
                    if( data.code ){
                        window.location.href='<?php echo url("cart"); ?>';
                    }else{
                    	$.hideLoading();
                        $.toast("提交失败！", "cancel");
                    }
                }
            })
		}
	})
		</script>


	</body>

</html>