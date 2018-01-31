/*计算总钱数*/
function total() {
	setTimeout(function() {
		var S = 0;
		$.each($('.total'), function() {
			var $ul_total = $(this).prev('ul');
			var s = 0;
			var n1 = 0;
			$.each($(this).prev('ul').find(".number"), function(i) {
				if($ul_total.eq(i)) {
					s = s + parseInt($(this).html()) * parseInt($(this).parent().prev().html().replace("￥", ""));
					n1 = n1 + parseInt($(this).html());
				}
			});
			$(this).children(".red").html("￥" + s.toFixed(2));
			$(this).children("number").html(n1);
			S = S + s;
		});
		$(".bottom-nav .merw").html(S.toFixed(2));
	}, 100)
}

$(function() {
	total();

	/*加*/
	$('.btn2').click(function() {
		$(this).prev('.number').html(parseInt($(this).prev('.number').html()) + 1);
		total();
	})

	/*减*/
	$('.btn1').click(function() {
		if($(this).next('.number').html() == 0)
			$(this).next('.number').html(0);
		else
			$(this).next('.number').html(parseInt($(this).next('.number').html()) - 1);
		total();
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
		$('.shuli').val($(this).html());
	})
	
	$('.btn').click(function() {
		var shuli = $('.shuli').val();
		    reg = /^[1-9]\d*$/;
		if(shuli == '') {
			$('.shuli').focus();
			$.toast('请输入数量！', 'text');
			return false;
		} else if(!reg.test(shuli)) {
			$.toast('请输入正整数?', 'text');
			return false;
		} else if($('.shuli').val() > 1000) {
			$.toast('超出库存了！', 'text');
			return false;
		}
		$("ul").eq($('.text1').attr('ind_1')).find(".number").eq($('.text1').attr('ind')).html($('.shuli').val());
		$('.text1').css({
			"display": "none",
			"-webkit-display": "none"
		});
		total();
	});
	
})

