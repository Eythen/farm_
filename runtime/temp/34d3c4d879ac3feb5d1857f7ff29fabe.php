<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/book/index.html";i:1516084570;}*/ ?>
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
		<title>预定信息</title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/book.css" rel="stylesheet">
		<style>
			.weui-picker-modal{ background: #fff;}
		</style>

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>

		<div class="weui-cells__title weui-cells-text">预定信息</div>
		<div class="weui-cells weui-cells_form">
			<div class="weui-cell">
				<div class="weui-cell__hd"><label class="weui-label">用餐人数</label></div>
				<div class="weui-cell__bd book-text">
					<input class="weui-input" id="numeral" type="number" pattern="[0-9]*" placeholder="请输入用餐人数（必填）" value="<?php echo $temp['people_num']; ?>">
				</div>
			</div>
			<div class="weui-cell weui-book">
				<div class="weui-cell__hd"><label class="weui-label">用餐时间</label></div>
				<div class="weui-cell__bd book-text">
					<input class="weui-input" id="time" type="text" value="" placeholder="请输入用餐时间（必填）">
				</div>
			</div>
			<div class="weui-cell weui-book">
				<div class="weui-cell__hd"><label class="weui-label">特别要求</label></div>
				
				<div class="weui-cell__bd book-text"><input class="weui-input" id="in" type="text" placeholder="请选择（非必选）" value="<?php echo $temp['remark']; ?>"></div>
			</div>
		</div>
		<div class="book-box"><p>注：如用餐者中有孕妇，建议选择少盐，少味精选项</div>
		
		<div class="weui-cells__title weui-cells-text">预定人信息</div>
		<div class="weui-cells weui-cells_form">
			<div class="weui-cell">
				<div class="weui-cell__hd"><label class="weui-label">姓名</label></div>
				<div class="weui-cell__bd">
					<input class="weui-input" id="name" type="text" placeholder="" value="<?php echo $temp['name']; ?>">
				</div>
			</div>
			<div class="weui-cell">
				<div class="weui-cell__hd"><label class="weui-label">电话</label></div>
				<div class="weui-cell__bd">
					<input class="weui-input" id="phone" type="number" pattern="[0-9]*" placeholder="" value="<?php echo $temp['mobile']; ?>">
				</div>
			</div>
		</div>
		
		<div class="book-margin">
			<input type="button" class="weui-btn yellow-color" id="submit" value="提交" />
		</div>

		<!--导航-->
		<nav class="footer">

			<a href="<?php echo url('adopt/index'); ?>">
				<div class="icon">
					<span class="icon-ziyuan5"></span>
				</div>
				<p class="tetx">认养</p>
			</a>

			<a href="<?php echo url('shop/index'); ?>">
				<div class="icon">
					<span class="icon-sc_w"></span>
				</div>
				<p class="tetx">商城</p>
			</a>

			<a href="<?php echo url('index/index'); ?>">
				<div class="icon">
					<span class="icon-ziyuan6"></span>
				</div>
				<p class="tetx">首页</p>
			</a>

			<a href="<?php echo url('book/index'); ?>" class="active">
				<div class="icon">
					<span class="icon-ziyuan2"></span>
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
		<script>
			$(function() {
				//日期
				$("#time").datetimePicker({
					min: "<?php echo date('Y-m-d'); ?>",
					title: '选择用餐时间',
					yearSplit: '年',
					monthSplit: '月',
					dateSplit: '日',
					times: function() {
						return [{
							values: ['上午8点', '下午2点', '晚上8点']
						}];
					},

					onChange: function(picker, values, displayValues) {
						console.log(values);
					}
				});
				/*$("#time").datetimePicker({
					title: '选择用餐时间',
					yearSplit: '年',
					monthSplit: '月',
					dateSplit: '日',
					times: function() {
						return [ // 自定义的时间
							{
								values: (function() {
									var hours = [];
									for(var i = 0; i < 24; i++) hours.push(i > 9 ? i : '0' + i);
									return hours;
								})()
							},
							{
								divider: true, // 这是一个分隔符
								content: '时'
							},
							{
								values: (function() {
									var minutes = [];
									for(var i = 0; i < 59; i++) minutes.push(i > 9 ? i : '0' + i);
									return minutes;
								})()
							},
							{
								divider: true, // 这是一个分隔符
								content: '分'
							}
						];
					},
					onChange: function(picker, values, displayValues) {
						console.log(values);
					}
				});*/
				
				//多选
				$('.user-sex input').each(function(){
	                var self = $(this),
	                        label = self.next(),
	                        label_text = label.text();
	                label.remove();
	                self.iCheck({
	                    checkboxClass: 'icheckbox',
	                    insert: label_text
	                });
	            });
	            $('.user-sex button').each(function(){
	                var self = $(this),
	                        label = self.next(),
	                        label_text = label.text();
	                label.remove();
	                self.iCheck({
	                    checkboxClass: 'icheckbox',
	                    insert: label_text
	                });
	            });
	            
	            
	            $('#submit').click(function(event) {
	            	
			    	var numeral = $('#numeral').val();
					    remark = $('#in').val();
			    	    time = $('#time').val();
			    	    regExp = /^\+?[1-9][0-9]*$/;
			    	    name = $('#name').val();
			    	    phone = $('#phone').val();
					    partten = /^1[3|4|5|7|8]\d{9}$/;
					if(numeral == '') {
						$('#numeral').focus();
						$.toptip('填写用餐人数', 'warning');
						return false;
					} else if(!regExp.test(numeral)) {
						$.toptip('请输入正整数数字', 'warning');
						return false;
					} else if(time == '') {
						$.toptip('请输入预定时间', 'warning');
						return false;
					} else if(name == '') {
						$.toptip('请输入姓名', 'warning');
						return false;
					} else if(phone == '') {
						$.toptip('请输入手机号码', 'warning');
						return false;
					} else if(!regExp.test(phone)) {
						$.toptip('您输入的手机号码不正确', 'warning');
						return false;
					} else {

						//$.toptip('提交成功!', 'warning');
						$.showLoading();
						$.ajax({
	                        url: "<?php echo url('index'); ?>",
	                        type: 'POST',
	                        dataType: 'json',
	                        data: {people_num: numeral, at_time: time, name:name, mobile:phone, remark:remark},
	                        success: function(data){
	                            if( data.code ){
	                                window.location.href='<?php echo url("table"); ?>';
	                            }else{
	                            	$.hideLoading();
	                                $.toast("提交失败！", "cancel");
	                            }
	                        }
	                    })
					}
			    });
			    
			    
			    $("#in").select({
					title: "特别要求",
					multi: true,
					/*min: 2,
					max: 3,*/
					items: [{
							title: "微辣",
							value: 1,
							description: ""
						},
						{
							title: "中辣",
							value: 2,
							description: ""
						},
						{
							title: "免辣",
							value: 3,
							description: ""
						},
						{
							title: "加辣",
							value: 4,
							description: ""
						},
						{
							title: "少油",
							value: 5,
							description: ""
						},
						{
							title: "少盐",
							value: 6,
							description: ""
						},
						{
							title: "少味精",
							value: 7,
							description: ""
						},
					],
					/*beforeClose: function(values, titles) {
						if(values.indexOf("6") !== -1) {
							$.toast("不能选睡觉", "cancel");
							return false;
						}
						return true;
					},*/
					onChange: function(d) {
						console.log(this, d);
					},
					onClose: function(d) {
						console.log('close')
					}
				});
			    
			});
		</script>

	</body>

</html>