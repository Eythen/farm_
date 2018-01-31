<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"/Applications/MAMP/htdocs/farm/public/../application/wap/view/book/cart.html";i:1516084570;}*/ ?>
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
		
		<title>购物车</title>

		<!-- css样式 -->
		<link href="__PUBLIC__/skin/css/weui.min.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/jquery-weui.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/font/iconfont.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/app.css" rel="stylesheet">
		<link href="__PUBLIC__/skin/css/cart.css" rel="stylesheet">
		<style>
			.weui-popup__overlay{
				opacity: 1;
			}
		</style>

		<!-- 自适应处理页面js -->
		<script src="__PUBLIC__/skin/js/flexible.js"></script>
	</head>

	<body ontouchstart>

		<header class="header">
			<a class="icon-jiantou-left" href="ordering.html"></a>
			<div class="header-title">购物车</div>
			<a class="txte-right" href="javascript:;">编辑</a>
		</header>

		<div class="cart">
			
			<div class="Dataempty">
				<div class="Dataempty_img">
					<img src="__PUBLIC__/skin/img/icon/emptyfile.png" />
					<p class="txte">暂无数据</p>
					<!--<div class="strollaround">
						<a href="ordering.html" class="cart-btn">去逛逛</a>
					</div>-->
				</div>
			</div>

			<div class="con">
				<div class="content">
					<ul>
						<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
						<li class="clearfix">
							<div class="label fl" data-id="<?php echo $vo['id']; ?>">
								<label data-id="<?php echo $vo['id']; ?>">
									<?php if($vo['selected'] == '1'): ?>
					    			<input type="checkbox" checked="checked" data-id="<?php echo $vo['id']; ?>" data-type="<?php echo $vo['type']; ?>"/>
					    			<img src="__PUBLIC__/skin/img/icon/on.png"/>
					    			<?php else: ?>
					    			<input type="checkbox" data-id="<?php echo $vo['id']; ?>" data-type="<?php echo $vo['type']; ?>"/>
					    			<img src="__PUBLIC__/skin/img/icon/off.png"/>
									<?php endif; ?>
					    		</label>
							</div>
							<div class="img"><img src="<?php echo $vo['original_img']; ?>" /></div>
							<div class="text">
								<p class="overflow"><?php echo $vo['goods_name']; ?></p>
							</div>
							<div class="text">
								<p class="over"><?php echo $vo['goods_remark']; ?></p>
								<p class="clearfix top">
									<small class="red">￥</small>
									<span class="red fs"><?php echo $vo['goods_price']; ?></span>
									<span class="m-spinner">
				    					<input type="button" value="" class="btn1" data-id="<?php echo $vo['id']; ?>"/>
				    					<span class="number"><?php echo $vo['goods_num']; ?></span>
									    <input type="button" value="" class="btn2" data-id="<?php echo $vo['id']; ?>"/>
									</span>
								</p>
							</div>
						</li>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
					<p class="total">一共<number></number>件商品:<small class="red">￥</small><span></span></p>
				</div>
			</div>

			<!--结算-->
			<div class="bottom">
				<div class="bottom-label">
					<label>
		    			<input type="checkbox"/>
		    			<img src="__PUBLIC__/skin/img/icon/off.png" />
		    			<b>全选</b>
		    		</label>
				</div>
				<div class="bottom-left">合计:<small class="red">￥</small>
					<span></span>
					<a href="reserved.html" class="sett yellow-color">结算</a>
				</div>
			</div>

			<!--删除-->
			<div class="bottom fixed" style="display: none;">
				<div class="fr">
					<button class="delete yellow-color">删除</button>
				</div>
			</div>

		</div>


		<!-- js -->
		<script src="__PUBLIC__/skin/js/jquery.min.js"></script>
		<script src="__PUBLIC__/skin/js/fastclick.js"></script>
		<script>
			$(function() {
				//消除延迟
				FastClick.attach(document.body);
			});
		</script>
		<script type="text/javascript">
			/*计算总钱数*/
			function total() {
				setTimeout(function() {
					var S = 0;
					$.each($('.total'), function() {
						var $ul_total = $(this).prev('ul').find("input[type='checkbox']");
						var s = 0;
						var n1 = 0;
						$.each($(this).prev('ul').find(".number"), function(i) {
							if($ul_total.eq(i).attr("checked") == "checked") {
								s = s + parseFloat($(this).html()) * parseFloat($(this).parent().prev().html().replace("￥", ""));
								n1 = n1 + parseFloat($(this).html());
							}
						});
						$(this).children("span").html("￥" + s.toFixed(2));
						$(this).children("number").html(n1);
						S = S + s;
					});
					$(".bottom span").html(S.toFixed(2));
				}, 100)
			}

			/*判断有无数据*/
			function hide() {
				if($(".content").length == 0) {
					$(".bottom").hide();
					$(".Dataempty").css("display", "-webkit-box");
					return;
				} else {
					$(".bottom").eq(0).show();
					$(".Dataempty").css("display", "none");
				}
			}

			/*判断是否全选*/
			function sum() {
				if($("ul input[checked='checked']").length == $("li").length) {
					$(".bottom input[type=checkbox]").attr("checked", "checked");
					$(".bottom input[type=checkbox]").next("img").attr("src", "/public/skin/img/icon/on.png");
				} else {
					$(".bottom input[type=checkbox]").removeAttr("checked");
					$(".bottom input[type=checkbox]").next("img").attr("src", "/public/skin/img/icon/off.png");
				}
			}

			/*给单选框或复选框添加样式*/
			function checkbox($this) {
				if($this.attr('type') == "checkbox") {
					if($this.attr('checked') == "checked") {
						$this.removeAttr("checked");
						$this.next('img').attr("src", "/public/skin/img/icon/off.png");
					} else {
						$this.attr("checked", "checked");
						$this.next('img').attr("src", "/public/skin/img/icon/on.png");
					}
				}
				total();
			}
			/*给单选框或复选框添加样式*/
			$(function() {
				hide();
				total();
				/*编辑*/
				$(".header .txte-right").click(function() {
					if($(this).html() == "编辑") {
						$(this).html("取消");
						$(".bottom").eq(1).show();
					} else {
						$(this).html("编辑");
						$(".bottom").eq(1).hide();
					}
					hide();
				});
				
				/*底部全选*/
				$('.bottom-label input').change(function() {
					if($(this).attr("checked") == "checked") {
						$(".con input[type='checkbox']").removeAttr("checked");
						$(".con input[type='checkbox']").next('img').attr("src", "/public/skin/img/icon/off.png");
					} else {
						$(".con input[type='checkbox']").attr("checked", "checked");
						$(".con input[type='checkbox']").next('img').attr("src", "/public/skin/img/icon/on.png");
					}
					checkbox($(this));
				})

				/*子项全选*/
				$('.list input').change(function() {
					var $list_input = $(this).parents('.list').next('ul').find('input[type=checkbox]');
					if($(this).attr("checked") == undefined) {
						$list_input.attr("checked", "checked");
						$list_input.next('img').attr("src", "/public/skin/img/icon/on.png");
					} else {
						$list_input.removeAttr("checked");
						$list_input.next('img').attr("src", "/public/skin/img/icon/off.png");
					}
					checkbox($(this));
					sum();
				})
				$("ul input[type='checkbox']").change(function() {
					checkbox($(this));
					var $ul_input = $(this).parents('ul').prev('.list').find('input');
					if($(this).parents('ul').find("input[checked='checked']").length == $(this).parents("ul").children('li').length) {
						$ul_input.attr("checked", "checked");
						$ul_input.next('img').attr("src", "/public/skin/img/icon/on.png");
					} else {
						$ul_input.removeAttr("checked");
						$ul_input.next('img').attr("src", "/public/skin/img/icon/off.png");
					}
					sum();
				})
				
				/*点击加一*/
				$('.btn2').click(function() {
					var num = parseInt($(this).prev('.number').html());
					var store_count = $(this).data("count");
					var id = $(this).data("id");

					if (num >= store_count){
			            $.toast("库存数量只有"+store_count, "text");
			            $(this).prev('.number').html(store_count);
			            total();
			            return false;
					}else{
			            $(this).prev('.number').html(num + 1);
			            total();
			            $.ajax({
			                url:'/index.php/wap/cart/ajaxEdit',
			                data:{'id':id,'type':'inc'},
			                type:'post',
			                dataType:'json',
			            })
					}
				})
				/*点击减一*/
				$('.btn1').click(function() {
					var num = parseInt($(this).next('.number').html());
			        var id = $(this).data("id");

					if(num <= 1){
			            $(this).next('.number').html(1);
			            total();
			            $.toast("数量不能小于 1", "text");
			            return false;
					}else{
			            $(this).next('.number').html(num - 1);
			            total();
			            /*$.ajax({
			                url:'/index.php/wap/cart/ajaxEdit',
			                data:{'id':id,'type':'dec'},
			                type:'post',
			                dataType:'json',
			            })*/
					}
				})
				
				/*点击减一*/
				$(".number").click(function() {
					$('.text1').css({
						"display": "flex",
						"-webkit-display": "flex"
					}).attr({
						'ind': $(this).parents('li').index(),
						"ind_1": $(this).parents("ul").attr("ind")
					});
					$('.text1 input[type=number]').val($(this).html());
				})
				$('.text1 input[type="button"]').click(function() {
					if($('.text1 input[type=number]').val() == "") {
						$('.alert').show().html('请输入数量！');
						setTimeout(function() {
							$('.alert').hide();
						}, 2000);
						return false;
					}
					if($('.text1 input[type=number]').val() > 1) {
						$('.alert').show().html('超出库存了！');
						setTimeout(function() {
							$('.alert').hide();
						}, 2000);
						return false;
					}
					$("ul").eq($('.text1').attr('ind_1')).find(".number").eq($('.text1').attr('ind')).html($('.text1 input[type=number]').val());
					$('.text1').css({
						"display": "none",
						"-webkit-display": "none"
					});
					total();
				})

				/*结算*/
				$('.sett').click(function() {
			        var ids = [];
			        $("ul input[type='checkbox']").each(function () {
			        	if ($(this).attr('checked') == "checked"){
			                ids.push($(this).data('id'));
						}
			        })
			        if (ids.length < 1){
			            $.toast("请选择要结算的商品！", "text");
			            return false;
					}
			        window.location.href="/index.php/wap/cart/order?ids="+ids;
			    });

				/*删除*/
				$('.delete').click(function() {
			        var ids = '';
					$.each($('li'), function() {
						var check = $(this).find("input[type=checkbox]");
						if(check.attr("checked") == "checked") {
							ids += check.data('id') + ',';
			                $(this).remove();
						}
					});
			        $.each($(".content"), function() {
			            if($(this).find("li").length == 0) {
			                $(this).remove();
			            }
			        });
			        hide();
			        total();


			        if(ids.length > 0){
			            $.ajax({
			                url:'/index.php/wap/book/ajaxDelCart',
			                data:{"ids":ids},
			                type:"post",
			                dataType:'json',
			                success:function (res) {
			                    if (res.code != 1){
			                        $.toast(res.msg, "text");
			                    }
			                }
			            })
					}
				});
				/*删除*/
			})

			function checkall() {
				var checkednum = '';
				var count = '';
				$(".clearfix .label label").each(function() {
					count++;
					if($(this).find("input[type=checkbox]").attr("checked") == "checked") {
						checkednum++;
					}
				});
				if(checkednum == count) {
					$(".bottom-label label input").attr("checked", "checked").next("img").attr("src", "/public/skin/img/icon/on.png");
					$(".list label input").attr("checked", "checked").next("img").attr("src", "/public/skin/img/icon/on.png");
				}
			}
			//单项选择		
			$('.fl').on('click', function(){
				var id = $(this).data('id'),
					selected = 0;
				if($(this).find('input').prop('checked')){
					selected = 1;
				}
				else{
					selected = 0;
				}
				console.log(selected);
				var data = {id:id, selected:selected};
				$.ajax({
	                url: "<?php echo url('cart',['act' => 'selected']); ?>",
	                type: 'POST',
	                dataType: 'json',
	                data: data,
	                success: function(data){
	                    if( data.code ){
	                        
	                    }else{
	                    	
	                        $.toast("操作有误！", "cancel");
	                    }
	                }
	            })
			})

			//全选
			$('.bottom-label').on('click', function(){
				var id = $(this).data('id'),
					selected = 0;
				if($(this).find('input').prop('checked')){
					selected = 1;
				}
				else{
					selected = 0;
				}
				console.log(selected);
				var data = {id:id, selected:selected};
				$.ajax({
	                url: "<?php echo url('cart',['act' => 'selectedAll']); ?>",
	                type: 'POST',
	                dataType: 'json',
	                data: data,
	                success: function(data){
	                    if( data.code ){
	                        
	                    }else{
	                    	
	                        $.toast("操作有误！", "cancel");
	                    }
	                }
	            })
			})
			//数量增减
			$('.btn1, .btn2').on('click', function(){
				var id = $(this).data('id'),
					flag = 0;
					goods_num = Number($(this).closest('.m-spinner').find('.number').text());
				if($(this).hasClass('btn1') ){
					if(goods_num>1){
						goods_num = goods_num - 1;
						flag = 1;
					}
				}
				else{
					goods_num = goods_num + 1;
					flag = 1;
				}
				if(flag){
					
					var data = {id:id, goods_num:goods_num};
					$.ajax({
		                url: "<?php echo url('cart',['act' => 'num']); ?>",
		                type: 'POST',
		                dataType: 'json',
		                data: data,
		                success: function(data){
		                    if( data.code ){
		                        
		                    }else{
		                    	
		                        $.toast("操作有误！", "cancel");
		                    }
		                }
		            })
				}
			})

			//删除
			/*$('.delete').on('click', function(){
				var ids = '';
				$('.content input[type=checkbox]').each(function(){
					if($(this).prop('checked')){
						var id = $(this).closest('.fl').data('id');
						ids += id+',';
					}
				})
					console.log(ids);
			})*/



		</script>
		<script src="__PUBLIC__/skin/js/jquery-weui.js"></script>
		<!-- <script src="__PUBLIC__/skin/js/cart.js"></script> -->
		

	</body>

</html>