$(function() {
	var curNavIndex = 0; //全部0; 待付款1; 待发货2; 待收货3; 已完成4;
	var mescrollArr = new Array(5); //4个菜单所对应的5个mescroll对象
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
					icon: "/public/skin/img/icon/emptyfile.png", //图标,默认null
					tip: "暂无相关数据~", //提示
					btntext: "去逛逛 ", //按钮,默认""
					btnClick: function() { //点击按钮的回调,默认null
						window.open('/index.php/wap/shop/index', "_self")
					}
				},
				clearEmptyId: clearEmptyId //相当于同时设置了clearId和empty.warpId; 简化写法;默认null
			}
		});
		return mescroll;
	}

	/*联网加载列表数据  page = {num:1, size:10}; num:当前页 从1开始, size:每页数据条数 */
	function getListData(page) {
		//联网加载数据
		// console.log("curNavIndex=" + curNavIndex + ", page.num=" + page.num);
		getListDataFromNet(curNavIndex, page.num, page.size, function(data) {
			//联网成功的回调,隐藏下拉刷新和上拉加载的状态;
			// console.log("data.length=" + data.length);
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

			var str =   '<div class="order-list-hd">' +
						    '<span>单号：<em>' + pd.order_sn + '</em></span>' +
						    '<span>' + pd.status + '</span>' +
					    '</div>';
			for(var k = 0; k < pd['goods'].length; k++){
				var pp = pd['goods'][k];
                str += '<a href="/index.php/wap/product/details.html?goods_id='+pp.goods_id+'">' +
						'<div class="order-box">' +
						'<div class="order-list-bd">' +
						'<div class="order-img"><img src="' + pp.original_img + '" alt=""></div>' +
						'<div class="order-text">' +
						'<div class="order-text-width">' +
						'<h1>' + pp.goods_name + '</h1>' +
						'</div>' +
						'<div class="order-text-right">' +
						'<div class="order-right-txte">' +
						'<span><small>¥</small>' + pp.shop_price + '</span><br>' +
						'<span class="order-text-mrk"><small>¥</small>' + pp.market_price + '</span>' +
						'</div>' +
						'</div>' +
						'<div class="clear">' +
						'<div class="wy-pro-pri fl">' + pp.goods_remark + '</div>' +
						'<div class="pro-amount fr"><span>×<em>' + pp.goods_num + '</em></span></div>' +
						'</div>';
				if(pp.is_send == 4){
					str += '<div class="clear-fr">退订中</div>';
				}else if(pp.is_send == 3){
                    str += '<div class="clear-fr">已退订</div>';
				}
				str	+=	'</div>' +
						'</div>' +
						'</div>' +
						'</div>' +
						'</a>' ;
			}
			str +=	'<div class="statistics">' +
				'<span>共<em class="number">' + pd.num + '</em>件商品，总金额：</span>' +
				'<span class="wy-pro-red">¥<em class="number">' + pd.order_amount + '</em></span>' +
				'</div><div class="weui-cell oder-btnbox">';

			if(pd.order_status != 3){
				if(pd.shipping_status > 0 && pd.order_status == 1){
					str+='<a class="ords-btn goods" href="/index.php/wap/users/order_confirm.html?order_id='+pd.order_id+'">确认收货</a>';
					str+='<a class="ords-btn" href="/index.php/wap/users/logistics.html?order_id='+pd.order_id+'">查看物流</a>';
				}
				if(pd.pay_status == 0){
					str+='<a class="ords-btn red" href="/index.php/wap/cart/payment.html?order_id='+pd.order_id+'">付款</a>';
				}
				if(pd.shipping_status == 0 && pd.pay_status == 0){
					str+='<a class="ords-btn cancel" data-id="'+pd.order_id+'">取消订单</a>';
				}
			}
			str+='<a class="ords-btn" href="/index.php/wap/cart/details.html?order_id='+pd.order_id+'">查看订单</a></div>';

                        //
						// '' +
						// 	'<a href="' + pd.pdlogistics + '" class="ords-btn">查看物流</a>' +
						// 	'<a href="javascript:;" class="ords-btn cancel">取消订单</a>' +
						// 	'<a href="' + pd.pdpay + '" class="ords-btn red">付款</a>' +
						// 	'<a href="javascript:;" class="ords-btn goods">确认收货</a>' +
						// 	'<a href="' + pd.pdcheckorder + '" class="ords-btn">查看订单</a>' +
						// '</div>';
			var liDom = document.createElement("li");
			liDom.innerHTML = str;
			listDom.appendChild(liDom);
		}
	}

	/*联网加载列表数据*/
	function getListDataFromNet(curNavIndex, pageNum, pageSize, successCallback, errorCallback) {
		$.ajax({
			type: 'post',
			data: {"type":curNavIndex,"page":pageNum},
			url: "/index.php/wap/users/ajaxOrder",
			dataType: 'json',
			success: function(data){
				var listData = [];
				for(var i = 0;i < data.length; i++){
                    listData.push(data[i]);
				}
				//回调
				successCallback(listData);
				popu()
			},
		});
	}
});

//弹窗
function  popu(){
	$('.cancel').click(function() {
		var id = $(this).data('id');
		$.confirm("您确定取消订单吗？", "取消订单", function() {
			window.open('/index.php/wap/users/cancel_order.html?id='+id+'', "_self")
		});
	});
}
