$(function() {
	var mescroll = new MeScroll("mescroll", {
		up: {
			clearEmptyId: "dataList",
			callback: getListData,
		}
	});
	function getListData(page) {
		// console.log("page.num==" + page.num);//联网加载数据
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
			var str='<div class="package-box-bj bounceInUp">';
			    str += '<div class="pro_list">';
			    str += '<a href="/index.php/wap/product/details?goods_id='+pd.goods_id+'">';
			    str += '<div class="pro_img"><img src="'+ pd.original_img +'" /></div>';
			    str += '</a>';
			    str += '<div class="pro_info">';
			    str += '<a href="/index.php/wap/product/details?goods_id='+pd.goods_id+'">';
			    str += '<h1>'+ pd.goods_name +'</h1>';
			    str += '</a>';
			    str += '<p>'+ pd.goods_remark +'</p>';
			    str += '<p class="pro_price">';
			    str += '<small>¥</small>';
			    str += '<span>'+ pd.shop_price +'</span>';
			    str += '<button type="button" class="icon-carttwo add-button" data-price="'+ pd.shop_price +'" data-proid="'+ pd.goods_id +'" data-proname="'+ pd.goods_name +'" data-proimg="'+ pd.original_img +'"></button>';
			    str +='</p>';
			    str +='</div>';
			    str +='</div>';
			    str +='</div>';
			var liDom = document.createElement("li");
			liDom.innerHTML = str;
			listDom.appendChild(liDom);
		}
	}
	var cat_id = 1;
	/*联网加载列表数据*/
	function getListDataFromNet(pageNum, pageSize, successCallback, errorCallback) {
        $.ajax({
            type: 'get',
            url: '/index.php/wap/product/ajaxlist',
            data: {"page":pageNum,"cat_id":cat_id},
            dataType: 'json',
            success: function(data) {
                //模拟分页数据
                var listData = [];
                for(var i=0;i<data.length;i++) {
                    listData.push(data[i]);
                }
                successCallback(listData);
                cart()
            },
        });

	}
});


//购物车
function  cart(id){
    var cartWrapper = $('.cd-cart-container');
	var productId = 0;
	if(cartWrapper.length > 0) {
		var cartBody = cartWrapper.find('.body')
		var cartList = cartBody.find('ul').eq(0);
		var cartTotal = cartWrapper.find('.checkout').find('span');
		var cartTrigger = cartWrapper.children('.cd-cart-trigger');
		var cartCount = cartTrigger.children('.count')
		//var addToCartBtn = $('.cd-add-to-cart');
		var addToCartBtn = $('.add-button');
		var undo = cartWrapper.find('.undo');
		var undoTimeoutId;
		addToCartBtn.off('click');
		addToCartBtn.on('click', function(event) {
			event.preventDefault();
			addToCart($(this));
		});
		cartTrigger.off('click');
		cartTrigger.on('click', function(event) {
			event.preventDefault();
			toggleCart();
		});
		cartWrapper.off('click');
		cartWrapper.on('click', function(event) {
			if($(event.target).is($(this)))
				toggleCart(true);
		});
		cartList.off('click');
		cartList.on('click', '.delete-item', function(event) {
			event.preventDefault();
			removeProduct($(event.target).parents('.product'));
		});
		cartList.on('change', 'select', function(event) {
			quickUpdateCart();
		});
		undo.off('click');
		undo.on('click', 'a', function(event) {
			clearInterval(undoTimeoutId);
			event.preventDefault();
			cartList.find('.deleted').addClass('undo-deleted').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function() {
				$(this).off('webkitAnimationEnd oanimationend msAnimationEnd animationend').removeClass('deleted undo-deleted').removeAttr('style');
				quickUpdateCart();
			});
			undo.removeClass('visible');
		});
	}
	function toggleCart(bool) {
		var cartIsOpen = (typeof bool === 'undefined') ? cartWrapper.hasClass('cart-open') : bool;
		if(cartIsOpen) {
			cartWrapper.removeClass('cart-open');
			clearInterval(undoTimeoutId);
			undo.removeClass('visible');
			cartList.find('.deleted').remove();
			setTimeout(function() {
				cartBody.scrollTop(0);
				if(Number(cartCount.find('li').eq(0).text()) == 0)
					cartWrapper.addClass('empty');
			});
		} else {
			cartWrapper.addClass('cart-open');
		}
	}
	function addToCart(trigger) {
		var cartIsEmpty = cartWrapper.hasClass('empty');
		var price = trigger.data('price'),
			proname = trigger.data('proname'),
			proid = trigger.data('proid'),
			proimg = trigger.data('proimg');

        var code = addCart(proid,'1',0);
        if(code){
            addProduct(proname, proid, price, proimg);
            updateCartCount(cartIsEmpty);
            updateCartTotal(trigger.data('price'), true);
            cartWrapper.removeClass('empty');
        }

	}
	function addProduct(proname, proid, price, proimg) {
		productId = productId + 1;
		var quantity = $("#cd-product-" + proid).text();
		var select = '',
			productAdded = '';
		//console.log(Number(quantity));
		//console.log(quantity);
		if(quantity == '') {
			var select = '<span class="select">x<i id="cd-product-' + proid + '">1</i></span>';
			var productAdded = $('<li class="product">'+
			                        '<div class="product-image">'+
			                            '<img src="' + proimg + '" alt="placeholder">'+
			                        '</div>'+
			                        '<div class="product-details">'+
			                            '<h3>' + proname + '</h3>'+
			                            '<span class="price">￥' + price + '</span>'+
			                            '<div class="actions">'+
			                                '<div class="quantity"><label for="cd-product-' + proid + '">份数</label>' + select + '</div>'+
			                            '</div>'+
			                        '</div>'+
			                    '</li>');
			cartList.prepend(productAdded);
		} else {
			quantity = parseInt(quantity);
			//var select = '<span class="select">x<i id="cd-product-'+proid+'">'+quantity+'</i></span>';
			$("#cd-product-" + proid).html(quantity + 1);
		}
		//var productAdded = $('<li class="product"><div class="product-image"><a href="#0"><img src="img/product-preview.png" alt="placeholder"></a></div><div class="product-details"><h3><a href="#0">'+proname+'</a></h3><span class="price">￥'+price+'</span><div class="actions"><a href="#0" class="delete-item">删除</a><div class="quantity"><label for="cd-product-'+ proid +'">件数x</label><span class="select"><select id="cd-product-'+ proid +'" name="quantity"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></span></div></div></div></li>');
		//cartList.prepend(productAdded);
	}
	function removeProduct(product) {
		clearInterval(undoTimeoutId);
		cartList.find('.deleted').remove();
		var topPosition = product.offset().top - cartBody.children('ul').offset().top,
			productQuantity = Number(product.find('.quantity').find('.select').find('i').text()),
			productTotPrice = Number(product.find('.price').text().replace('￥', '')) * productQuantity;
		product.css('top', topPosition + 'px').addClass('deleted');
		updateCartTotal(productTotPrice, false);
		updateCartCount(true, -productQuantity);
		undo.addClass('visible');
		undoTimeoutId = setTimeout(function() {
			undo.removeClass('visible');
			cartList.find('.deleted').remove();
		}, 8000);
	}
	function quickUpdateCart() {
		var quantity = 0;
		var price = 0;
		cartList.children('li:not(.deleted)').each(function() {
			var singleQuantity = Number($(this).find('.select').find('i').text());
			quantity = quantity + singleQuantity;
			price = price + singleQuantity * Number($(this).find('.price').text().replace('￥', ''));
		});
		cartTotal.text(price.toFixed(2));
		cartCount.find('li').eq(0).text(quantity);
		cartCount.find('li').eq(1).text(quantity + 1);
	}
	function updateCartCount(emptyCart, quantity) {
		if(typeof quantity === 'undefined') {
			var actual = Number(cartCount.find('li').eq(0).text()) + 1;
			var next = actual + 1;
			if(emptyCart) {
				cartCount.find('li').eq(0).text(actual);
				cartCount.find('li').eq(1).text(next);
			} else {
				cartCount.addClass('update-count');

				setTimeout(function() {
					cartCount.find('li').eq(0).text(actual);
				}, 150);

				setTimeout(function() {
					cartCount.removeClass('update-count');
				}, 200);

				setTimeout(function() {
					cartCount.find('li').eq(1).text(next);
				}, 230);
			}
		} else {
			var actual = Number(cartCount.find('li').eq(0).text()) + quantity;
			var next = actual + 1;

			cartCount.find('li').eq(0).text(actual);
			cartCount.find('li').eq(1).text(next);
		}
	}

	function updateCartTotal(price, bool) {
		bool ? cartTotal.text((Number(cartTotal.text()) + Number(price)).toFixed(2)) : cartTotal.text((Number(cartTotal.text()) - Number(price)).toFixed(2));
	}
}
