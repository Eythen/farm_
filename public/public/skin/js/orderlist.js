$(function() {
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
					icon: "../img/icon/emptyfile.png", //图标,默认null
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
				str += '<div class="location">位置：<span>'+ pd.pdposition +'</span></div>';
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
				str += '<div class="order-price-left">已付订金：<span><small>￥</small><i>'+ pd.pddepositpaid +'</i></span></div>';
				str += '<div class="order-price-left">未付余额：<span><small>￥</small><i>'+ pd.pdunpaidbalance +'</i></span></div>';
				str += '</div>';
				str += '<div class="order-btn">';
				if(pd.pdreserved == '已预订'){
					str += '<a href="javascript:;" class="orderlistbtn cancel">取消订单</a>';
					str += '<a href="' + pd.pdurl + '" class="orderlistbtn red">付款</a>';
					str += '<a href="' + pd.pdurl + '" class="orderlistbtn red">支付余额</a>';
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
		setTimeout(function() {
          	/*$.ajax({
                type: 'GET',
                url: 'xxx',
                url: 'xxx?curNavIndex='+curNavIndex+'&num='+pageNum+'&size='+pageSize,
                dataType: 'json',
                success: function(data){*/
					var data = orderlistjson; // 模拟数据
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
			  	/*},
			    error: errorCallback
			});*/
		}, 500)
	}

	//禁止PC浏览器拖拽图片,避免与下拉刷新冲突;如果仅在移动端使用,可删除此代码
	document.ondragstart = function() {
		return false;
	}
});

//弹窗
function  popu(id){
	$('.cancel').click(function(event) {
		$.confirm("您确定取消订单吗？", "取消订单", function() {
			window.open('javascript:;', "_self")
		}, function() {
			$.toast("您已取消订单", "text");
		});
	});
}