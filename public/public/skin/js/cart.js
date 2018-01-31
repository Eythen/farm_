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
            $.ajax({
                url:'/index.php/wap/cart/ajaxEdit',
                data:{'id':id,'type':'dec'},
                type:'post',
                dataType:'json',
            })
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
        var ids = [];
		$.each($('li'), function() {
			var check = $(this).find("input[type=checkbox]");
			if(check.attr("checked") == "checked") {
				ids.push(check.data('id'));
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
                url:'/index.php/wap/cart/ajaxDelCart',
                data:{"ids":ids},
                type:"post",
                dataType:'json',
                success:function (res) {
                    if (res.status != 1){
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