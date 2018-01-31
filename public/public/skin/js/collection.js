$(function() {
	var mescroll = new MeScroll("mescroll", {
		up: {
			clearEmptyId: "dataList",
			callback: getListData,
		}
	});
	function getListData(page) {
		console.log("page.num==" + page.num);//联网加载数据
		getListDataFromNet(page.num, page.size, function(data) {
			mescroll.endSuccess(data.length); //传参:数据的总数; mescroll会自动判断列表如果无任何数据,则提示空;列表无下一页数据,则提示无更多数据;
			setListData(data);
		}, function() {
			mescroll.endErr();
		});
	}
	/*设置列表数据*/
	function setListData(data) {
		var listDom = document.getElementById("dataList");
		for(var i = 0; i < data.length; i++) {
			var pd = data[i];
			var str = '<a href="' + pd.pdurl + '">' +
							'<div class="collection-box">' +
								'<img src="' + pd.pdimg + '">' +
							'</div>' +
							'<div class="weui-media-box__bd">' +
								'<h4 class="collection-box-title">' + pd.pdname + '</h4>' +
								'<p class="weui-media-box__desc">' + pd.pdsold + '</p>' +
								'<p class="media-mony"><small>¥</small><span>' + pd.pdmoney + '</span></p>' +
							'</div>' +
					   '</a>';
			var liDom = document.createElement("li");
			liDom.innerHTML = str;
			listDom.appendChild(liDom);
		}
	}
	var dataTag = 1;
	/*联网加载列表数据*/
	function getListDataFromNet(pageNum, pageSize, successCallback, errorCallback) {
		//延时一秒,模拟联网
		setTimeout(function() {
			/*$.ajax({
				type: 'GET',
				url: 'xxx',
				url: 'xxx?num=' + pageNum + "&size=" + pageSize,
				dataType: 'json',
				success: function(data) {*/
					var data = collectionjson; // 模拟数据
					//模拟分页数据
					var listData = [];
					for(var i = (pageNum - 1) * pageSize; i < pageNum * pageSize; i++) {
						if(i == data.length) break;
						listData.push(data[i]);
					}
					successCallback(listData);
				/*},
				error: errorCallback
			});*/
		}, 1000)
	}
	//禁止PC浏览器拖拽图片,避免与下拉刷新冲突;如果仅在移动端使用,可删除此代码
	/*document.ondragstart = function() {
		return false;
	}*/
});