$(function () {
    var itemIndex = 0;

    var tabLoadEndArray = [false, false, false, false, false];
    var tabLenghtArray = [28, 15, 47, 20, 30];
    var tabScroolTopArray = [0, 0, 0, 0, 0];
    //加载订单列表
    var dropload = $('.khfxWarp').dropload({
        scrollArea: window,
        domDown: {
            domClass: 'dropload-down',
            domRefresh: '<div class="dropload-refresh">上拉加载更多</div>',
            domLoad: '<div class="dropload-load"><span class="loading"></span>加载中...</div>',
            domNoData: '<div class="dropload-noData">已无数据</div>'
        },
        loadDownFn: function (me) {
            setTimeout(function () {
                if (tabLoadEndArray[itemIndex]) {
                    me.resetload();
                    me.lock();
                    me.noData();
                    me.resetload();
                    return;
                }
                var result = '';
                for (var index = 0; index < 10; index++) {
                    if (tabLenghtArray[itemIndex] > 0) {
                        tabLenghtArray[itemIndex]--;
                    } else {
                        tabLoadEndArray[itemIndex] = true;
                        break;
                    }
                    if (itemIndex == 0) {
                        result
                        += ''
	                    + '    <li>'
			            + '		<div class="daifk"><a href="">待付款</a></div>'
			            + '		<div class="service">'
			            + '			<a href="">'
			            + '				<div class="imgdi">'
			            + '					<img src="../img/goods_thumb.jpeg">'
			            + '				</div>'
			            + '				<div class="txte">'
			            + '					<p>山西特产 唐明园 特级石磨面粉2500g特级石磨面粉2500g</p>'
			            + '					<p><em>白色</em><em>数量x1</em></p>'
			            + '					<p class="luse">500积分</p>'
			            + '				</div>'
			            + '			</a>'
			            + '		</div>'
			            + '		<div class="daifk line">'
			            + '			<a class="dfk red" href="">待付款</a>'
			            + '			<a class="dfk" href="">订单详情</a>'
			            + '			<a class="dfk" href="">取消订单</a>'
			            + '		</div>'
			            + '	</li>';
                    } else if (itemIndex == 1) {
                        result
                        += ''
	                    + '    <li>'
			            + '		<div class="daifk"><a href="">待付款</a></div>'
			            + '		<div class="service">'
			            + '			<a href="">'
			            + '				<div class="imgdi">'
			            + '					<img src="../img/goods_thumb.jpeg">'
			            + '				</div>'
			            + '				<div class="txte">'
			            + '					<p>山西特产 唐明园 特级石磨面粉2500g特级石磨面粉2500g</p>'
			            + '					<p><em>白色</em><em>数量x1</em></p>'
			            + '					<p class="luse">500积分</p>'
			            + '				</div>'
			            + '			</a>'
			            + '		</div>'
			            + '		<div class="daifk line">'
			            + '			<a class="dfk red" href="">待付款</a>'
			            + '			<a class="dfk" href="">订单详情</a>'
			            + '			<a class="dfk" href="">取消订单</a>'
			            + '		</div>'
			            + '	</li>';
                    } else if (itemIndex == 2) {
                        result
                        += ''
	                    + '    <li>'
			            + '		<div class="daifk"><a href="">待发货</a></div>'
			            + '		<div class="service">'
			            + '			<a href="">'
			            + '				<div class="imgdi">'
			            + '					<img src="../img/goods_thumb.jpeg">'
			            + '				</div>'
			            + '				<div class="txte">'
			            + '					<p>山西特产 唐明园 特级石磨面粉2500g特级石磨面粉2500g</p>'
			            + '					<p><em>白色</em><em>数量x1</em></p>'
			            + '					<p class="luse">500积分</p>'
			            + '				</div>'
			            + '			</a>'
			            + '		</div>'
			            + '		<div class="daifk line">'
			            + '			<a class="dfk red" href="">催发</a>'
			            + '			<a class="dfk" href="">订单详情</a>'
			            + '			<a class="dfk" href="">取消订单</a>'
			            + '		</div>'
			            + '	</li>';
                    } else if (itemIndex == 3) {
                        result
                        += ''
	                    + '    <li>'
			            + '		<div class="daifk"><a href="">卖家已发货</a></div>'
			            + '		<div class="service">'
			            + '			<a href="">'
			            + '				<div class="imgdi">'
			            + '					<img src="../img/goods_thumb.jpeg">'
			            + '				</div>'
			            + '				<div class="txte">'
			            + '					<p>山西特产 唐明园 特级石磨面粉2500g特级石磨面粉2500g</p>'
			            + '					<p><em>白色</em><em>数量x1</em></p>'
			            + '					<p class="luse">500积分</p>'
			            + '				</div>'
			            + '			</a>'
			            + '		</div>'
			            + '		<div class="daifk line">'
			            + '			<a class="dfk red" href="">订单详情</a>'
			            + '			<a class="dfk" href="">查看物流</a>'
			            + '			<a class="dfk" href="">申请售后</a>'
			            + '		</div>'
			            + '	</li>';
			        } else if (itemIndex == 4) {
                        result
                        += ''
	                    + '    <li>'
			            + '		<div class="daifk"><a href="">交易成功</a></div>'
			            + '		<div class="service">'
			            + '			<a href="">'
			            + '				<div class="imgdi">'
			            + '					<img src="../img/goods_thumb.jpeg">'
			            + '				</div>'
			            + '				<div class="txte">'
			            + '					<p>山西特产 唐明园 特级石磨面粉2500g特级石磨面粉2500g</p>'
			            + '					<p><em>白色</em><em>数量x1</em></p>'
			            + '					<p class="luse">500积分</p>'
			            + '				</div>'
			            + '			</a>'
			            + '		</div>'
			            + '		<div class="daifk line">'
			            + '			<a class="dfk" href="">删除订单</a>'
			            + '		</div>'
			            + '	</li>';
			        }
                }
                $('.Cservice').eq(itemIndex).append(result);
                me.resetload();
            }, 500);
        }
    });
    $('.tabHead span').on('click', function () {
        tabScroolTopArray[itemIndex] = $(window).scrollTop();
        var $this = $(this);
        itemIndex = $this.index();
        $(window).scrollTop(tabScroolTopArray[itemIndex]);
        $(this).addClass('active').siblings('.tabHead span').removeClass('active');
        $('.tabHead .border').css('left', $(this).offset().left + 'px');
        $('.khfxPane').eq(itemIndex).show().siblings('.khfxPane').hide();
        if (!tabLoadEndArray[itemIndex]) {
            dropload.unlock();
            dropload.noData(false);
        } else {
            dropload.lock('down');
            dropload.noData();
        }
        dropload.resetload();
    });
});