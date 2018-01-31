

	//数据加载
	var mescroll = new MeScroll("mescroll", {
		up: {
			clearEmptyId: "dataList",
			callback: getListData,
		}
	});
	function getListData(page) {
		console.log("page.num==" + page.num); //page = {num:1, size:10};每页数据条数
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
			    str += '<div class="menu-img"><img src="'+ pd.pdimg +'" /></div>';
			    str += '<div class="menu-txt">';
				str += '<h4>'+ pd.pdname +'</h4>';
				str += '<p>'+ pd.pdtion +'</p>';
				str += '<div class="ordering-price">';
				str += '<h2><smll>￥</smll><span>'+ pd.pdprice +'</span></h2>';
				str += '<div class="btn">';
				str += '<button class="minus"><strong class="btn1"></strong></button>';
				str += '<i>0</i>';
				str += '<button class="add"><strong class="btn2"></strong></button>';
				str += '<i class="price">'+ pd.pdprice +'</i>';
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
			/*$.ajax({
				type: 'GET',
				url: 'xxx',
				url: 'xxx?num=' + pageNum + "&size=" + pageSize,
				dataType: 'json',
				success: function(data) {*/
					var data = orderingjson; // 模拟数据
		
					var listData = [];
					for(var i = (pageNum - 1) * pageSize; i < pageNum * pageSize; i++) {
						if(i == data.length) break;
						listData.push(data[i]);
					}
					successCallback(listData);
					jan()
			    /*},
				error: errorCallback
			});*/
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