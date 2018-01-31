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
					s = s + parseInt($(this).html()) * parseInt($(this).parent().prev().html().replace("￥", ""));
					n1 = n1 + parseInt($(this).html());
				}
			});
			$(this).children("span").html("￥" + s.toFixed(1));
			$(this).children("number").html(n1);
			S = S + s;
		});
		$(".bottom span").html(S.toFixed(1));
	}, 100)
}
/*计算总钱数*/
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
/*判断有无数据*/
/*判断是否全选*/
function sum() {
	if($("ul input[checked='checked']").length == $("li").length) {
		$(".bottom input[type=checkbox]").attr("checked", "checked");
		$(".bottom input[type=checkbox]").next("img").attr("src", "../../../public/img/c_checkbox_on.png");
	} else {
		$(".bottom input[type=checkbox]").removeAttr("checked");
		$(".bottom input[type=checkbox]").next("img").attr("src", "../../../public/img/c_checkbox_off.png");
	}
}
/*判断是否全选*/
/*给单选框或复选框添加样式*/
function checkbox($this) {
    var selected = '';
    var id = $this.val();
	if($this.attr('type') == "checkbox") {
		if($this.attr('checked') == "checked") {
            selected = 1;
			$this.removeAttr("checked");
			$this.next('img').attr("src", "../../../public/img/c_checkbox_off.png");
		} else {
            selected = 2;
			$this.attr("checked", "checked");
			$this.next('img').attr("src", "../../../public/img/c_checkbox_on.png");
		}
	}
	/*计算总钱数*/
	total();
	/*计算总钱数*/

    $.ajax({
        type:"post",
        url:"ajaxEdit",
        data:{"selected":selected,"id":id},
        dataType:'json',
        success:function(data){
        }
    });
}
/*给单选框或复选框添加样式*/
$(function() {
	hide();
	total();
	/*编辑*/
	$("header .edit").click(function() {
		if($(this).html() == "编辑") {
			$(this).html("完成");
			$(".bottom").eq(1).show();
		} else {
			$(this).html("编辑");
			$(".bottom").eq(1).hide();
		}
		hide();
	});
	/*编辑*/
	/*底部全选*/
	$('.bottom-label input').change(function() {
        var selected = '';
		if($(this).attr("checked") == "checked") {
            selected = 1;
			$(".con input[type='checkbox']").removeAttr("checked");
			$(".con input[type='checkbox']").next('img').attr("src", "../../../public/img/c_checkbox_off.png");
		} else {
            selected = 2;
			$(".con input[type='checkbox']").attr("checked", "checked");
			$(".con input[type='checkbox']").next('img').attr("src", "../../../public/img/c_checkbox_on.png");
		}
		checkbox($(this));

        $.ajax({
            type:"post",
            url:"ajaxEdit",
            data:{"selected":selected},
            dataType:'json',
            success:function(data){
            }
        });
	})
	/*底部全选*/
	/*子项全选*/
	$('.list input').change(function() {
        var selected = '';
		var $list_input = $(this).parents('.list').next('ul').find('input[type=checkbox]');
		if($(this).attr("checked") == undefined) {
            selected = 2;
			$list_input.attr("checked", "checked");
			$list_input.next('img').attr("src", "../../../public/img/c_checkbox_on.png");
		} else {
            selected = 1;
			$list_input.removeAttr("checked");
			$list_input.next('img').attr("src", "../../../public/img/c_checkbox_off.png");
		}
		checkbox($(this));
		sum();

        $.ajax({
            type:"post",
            url:"ajaxEdit",
            data:{"selected":selected},
            dataType:'json',
            success:function(data){
            }
        });
	})
	/*子项全选*/
	$("ul input[type='checkbox']").change(function() {
		checkbox($(this));
		var $ul_input = $(this).parents('ul').prev('.list').find('input');
		if($(this).parents('ul').find("input[checked='checked']").length == $(this).parents("ul").children('li').length) {
			$ul_input.attr("checked", "checked");
			$ul_input.next('img').attr("src", "../../../public/img/c_checkbox_on.png");
		} else {
			$ul_input.removeAttr("checked");
			$ul_input.next('img').attr("src", "../../../public/img/c_checkbox_off.png");
		}
		sum();
	})
	/*点击加一*/
	$('.btn2').click(function() {
		var maxnum = $(this).siblings('.maxnum').val();
		var ise = $(this).prev('.number').html();
        checkall();
        if (parseInt(ise) >= parseInt(maxnum)){
            layer.msg("库存数量不足！");
            return false;
        }
		$(this).prev('.number').html(parseInt($(this).prev('.number').html()) + 1);
		/*计算总钱数*/
		total();
		/*计算总钱数*/

		var num = $(this).prev('.number').html();
        var id = $(this).siblings('.id').val();
        $.ajax({
            type:"post",
            url:"ajaxEdit",
            data:{"num":num,"id":id},
            dataType:'json',
            success:function(data){
            }
        });

	})
	/*点击加一*/
	/*点击减一*/
	$('.btn1').click(function() {
		if($(this).next('.number').html() == 1){
            $(this).next('.number').html(1);
            return false;
		}else
			$(this).next('.number').html(parseInt($(this).next('.number').html()) - 1);
		/*计算总钱数*/
		total();
		/*计算总钱数*/

        var num = $(this).next('.number').html();
        var id = $(this).siblings('.id').val();
        $.ajax({
            type:"post",
            url:"ajaxEdit",
            data:{"num":num,"id":id},
            dataType:'json',
            success:function(data){
            }
        });

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
	/*删除*/
	$('.delete').click(function() {
		var ids = "";
		$.each($('li'), function() {
			if($(this).find("input[type=checkbox]").attr("checked") == "checked") {
                ids = ids + ',' + $(this).find("input[type=checkbox]").val();
				$(this).remove();
			}
		});
        $.ajax({
            type:"post",
            url:"ajaxDelCart",
            data:{"ids":ids},
            dataType:'json',
            success:function(data){
                if(data.status == 1){
                    layer.msg(data.msg);
                }else{
                    layer.msg(data.msg);
                    return false;
                }
            }
        });
		$.each($(".content"), function() {
			if($(this).find("li").length == 0) {
				$(this).remove();
			}
		});
		hide();
		total();
	});
	/*删除*/
    checkall();
});

function checkall() {
    var checkednum = '';
    var count = '';
	$(".clearfix .label label").each(function () {
        count++;
        if ($(this).find("input[type=checkbox]").attr("checked") == "checked"){
            checkednum++;
		}
    });
	if (checkednum == count){
		$(".bottom-label label input").attr("checked", "checked").next("img").attr("src", "../../../public/img/c_checkbox_on.png");
		$(".list label input").attr("checked", "checked").next("img").attr("src", "../../../public/img/c_checkbox_on.png");
	}
}