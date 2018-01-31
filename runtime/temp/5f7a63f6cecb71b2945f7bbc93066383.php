<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:79:"/Applications/MAMP/htdocs/farm/public/../application/home/view/book/detail.html";i:1516160992;s:78:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/css.html";i:1516084571;s:77:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/js.html";i:1516084571;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>信息管理平台</title>
        <link href="__PUBLIC__/img/favicon.ico" rel="shortcut icon" >
    <link href="__PUBLIC__/css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
    <link href="__PUBLIC__/css/bootstrap-theme.min.css?v=3.3.5" rel="stylesheet">
    <link href="__PUBLIC__/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="__PUBLIC__/css/animate.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/plugins/bootstraptable/bootstrap-table.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/plugins/bootstrapselect/bootstrap-select.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/plugins/datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/plugins/info/information.css" rel="stylesheet">
    <style type="text/css">
        html{background-color: #f5f5f5}
        .table thead {background-color: #ccc;}
        .margin{margin-right:1em;}


    </style>
</head>
<body>
    <div class="panel panel-info well">
    <section class="content">
    <div class="row">
      <div class="col-xs-12">
      	<div class="box">
           <nav class="navbar navbar-default">	     
			<div class="collapse navbar-collapse">
                <div class="navbar-form pull-right margin">
                      <!-- <?php if($order['order_status'] < 2): ?>
                         <a href="<?php echo url('home/order/editOrder',array('order_id'=>$order['order_id'])); ?>" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="编辑">修改订单</a>
                      <?php endif; ?> -->
                     <!--  <?php if(($split == 1) and ($order['order_status'] < 2)): ?>
                        <a href="<?php echo url('home/order/split_order',array('order_id'=>$order['order_id'])); ?>" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="编辑">拆分订单</a>
                     <?php endif; ?>-->
                     <a href="<?php echo url('editOrder',array('order_id'=>$order['order_id'], 'act' => 'print')); ?>" target="_blank" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="打印订单">
                                              <i class="fa fa-print"></i>打印订单
                                           </a>
                      <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
               </div>
            </div>
           </nav>
   
        <!--新订单列表 基本信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">基本信息</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>订单 ID:</td>
                            <td>订单号:</td>
                            <td>下单人:</td>
                            <td>电话:</td>
                            <td>应付:</td>
                            <td>订单 状态:</td>
                            <td>下单时间:</td>
                            <td>支付时间:</td>
                        </tr>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['order_sn']; ?></td>
                            <td><?php echo $order['name']; ?></td>
                            <td><?php echo $order['mobile']; ?></td>
                            <td><?php echo $order['order_amount']; ?></td>
                            <td id="order-status"><?php echo $order_status[$order['order_status']]; ?> / <?php echo $pay_status[$order['pay_status']]; ?></td>
                            <td><?php echo date('Y-m-d H:i',$order['add_time']); ?></td>
                            <td>
                                <?php if($order['pay_time'] != 0): ?>
                                    <?php echo date('Y-m-d H:i',$order['pay_time']); else: ?>
                                    N
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--新订单列表 收货人信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">订餐信息</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>用餐时间:</td>
                            <td>座位:</td>
                            <td>人数:</td>
                            <td>备注:</td>
                        </tr>
                        <tr>
                            <td><?php echo $order['at_time']; ?></td>
                            <td><?php echo $order['table']; ?></td>
                            <td><?php echo $order['people_num']; ?></td>
                            <td><?php echo $order['user_note']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--新订单列表 商品信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">商品信息 </h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td class="text-left">商品</td>
                        <td class="text-right">数量</td>
                        <td class="text-right">单品价格</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($order['order_combo']) || $order['order_combo'] instanceof \think\Collection || $order['order_combo'] instanceof \think\Paginator): $i = 0; $__LIST__ = $order['order_combo'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$good): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <td class="text-left"><?php echo $good['goods_name']; ?></td>
                            <td class="text-right"><?php echo $good['goods_num']; ?></td>
                            <td class="text-right"><?php echo $good['goods_price']; ?></td>
                        </tr>
                        <tr class="bg-info">
                            <td colspan="3" class="text-left"><?php echo $good['goods_name']; ?>:<?php echo $good['goods_content']; ?></td>
                            
                        </tr>
                    <?php endforeach; endif; else: echo "" ;endif; if(is_array($order['order_goods']) || $order['order_goods'] instanceof \think\Collection || $order['order_goods'] instanceof \think\Paginator): $i = 0; $__LIST__ = $order['order_goods'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$good): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <td class="text-left"><?php echo $good['goods_name']; ?></td>
                            <td class="text-right"><?php echo $good['goods_num']; ?></td>
                            <td class="text-right"><?php echo $good['goods_price']; ?></td>
                        </tr>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                    <tr>
                        <td colspan="2" class="text-right">小计:</td>
                        <td class="text-right"><?php echo $order['order_amount']; ?></td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
        <!--新订单列表 费用信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">费用信息
                    <!-- <a class="btn btn-primary btn-xs" data-original-title="修改费用" title="" data-toggle="tooltip" href="<?php echo url('home/Order/editprice',array('order_id'=>$order['order_id'])); ?>">
                        <i class="fa fa-pencil"></i>
                    </a> -->
                </h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td class="text-right">总金额:</td>
                        <td class="text-right">已付:</td>
                        <td class="text-right">未付:</td>
                    </tr>
                    <tr>
                        <td class="text-right"><?php echo $order['order_amount']; ?></td>
                        <td class="text-right"><?php echo $order['pay_money']; ?></td>
                        <td class="text-right"><?php echo $order['pay_money_spare']; ?></td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
        <!--新订单列表 操作信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">操作信息</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <div class="row">
                            <td class="text-right col-sm-2"><p class="margin">操作备注：</p></td>
                            <td colspan="3">
                                <form id="order-action">
                                    <textarea name="note" id="note" placeholder="请输入操作备注" rows="3" class="form-control"></textarea>
                                </form>
                            </td>
                        </div>
                    </tr>
                    <tr>
                        <div class="row">
                            <td class="text-right col-sm-2"><p class="margin">当前可执行操作：</p></td>
                            <td colspan="3">
                                <div class="input-group">
                                	   <!-- 0待处理，1已完成，2退订中3无效 4未到店消费（退款成功） -->
                                		<?php if(!in_array($order['order_status'], [1,2,3,4])): ?>                                               
                                			<button class="btn btn-primary margin action" data-url="<?php echo url('orderAction',array('order_id'=>$order['order_id'], 'act' => 'success', 'order_status' => 1)); ?>">完成</button>
                                        <?php endif; if(($order['order_status'] == '2') && !empty($order['pay_status'])): ?>
                                            <button class="btn btn-danger margin action" data-url="<?php echo url('orderAction',array('order_id'=>$order['order_id'], 'act' => 'refund') ); ?>">退款确认</button>可能需要退款<?php echo $order['refund_amount']; endif; if(empty($order['order_status']) && empty($order['pay_status'])): ?>
                                            <button class="btn btn-warning margin action" data-url="<?php echo url('orderAction',array('order_id'=>$order['order_id'], 'act' => 'success', 'order_status' => 3) ); ?>">无效</button>
                                        <?php endif; ?>
                                        
                                	                               
                                </div>
                            </td>
                        </div>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!--新订单列表 操作记录信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">操作记录</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td class="text-center">操作者</td>
                        <td class="text-center">操作时间</td>
                        <td class="text-center">订单状态</td>
                        <td class="text-center">付款状态</td>
                        
 
                        <td class="text-center">备注</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($action_log) || $action_log instanceof \think\Collection || $action_log instanceof \think\Paginator): $i = 0; $__LIST__ = $action_log;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$log): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <td class="text-center"><?php echo $log['action_user']; ?></td>
                            <td class="text-center"><?php echo date('Y-m-d H:i:s',$log['log_time']); ?></td>
                            <td class="text-center"><?php echo $order_status[$log['order_status']]; ?></td>
                            <td class="text-center"><?php echo $pay_status[$log['pay_status']]; ?></td>
                            
                            <td class="text-center"><?php echo $log['action_note']; ?></td>
                        </tr>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
            </div>
          </div>
        </div>
	  </div>
    </div> 
   </section>
</div>
</body>
	<script type="text/javascript">
		var publicUrl = '__PUBLIC__';
		var imgUrl    = '__PUBLIC__/img/';
		var jsUrl     = '__PUBLIC__/js/';
	</script>
	<script type="text/javascript" src="__PUBLIC__/js/jquery.min.js?v=1.9"></script>
	<script type="text/javascript" src="__PUBLIC__/js/bootstrap.min.js?v=3.3.5"></script>
	<script type="text/javascript" src="__PUBLIC__/js/information.js"></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/layer/layer.js?v=2.2"></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/bootstraptable/bootstrap-table.min.js" ></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/bootstraptable/bootstrap-table-zh-CN.js" ></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/bootstrapselect/bootstrap-select.min.js" ></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/datetimepicker/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/datetimepicker/bootstrap-datetimepicker.zh-CN.js"></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/upload/jquery.uploadify.min.js" ></script>
	<script type="text/javascript" src="__PUBLIC__/js/plugins/chosen/chosen.jquery.min.js" ></script>
	<script type="text/javascript" src="__PUBLIC__/js/myFormValidate.js"></script>

<script type="text/javascript">
    function dayin(){
        document.all('qingkongyema').click();
        $("#print").hide();
        $("#think_page_trace_open").hide();
        window.print();
        $("#print").show();
    }
</script>

<script language="VBScript">
    dim hkey_root,hkey_path,hkey_key
    hkey_root="HKEY_CURRENT_USER"
    hkey_path="/Software/Microsoft/Internet Explorer/PageSetup"
    //设置网页打印的页眉页脚为空
    function pagesetup_null()
    on error resume next
    Set RegWsh = CreateObject("WScript.Shell")
    hkey_key="/header" 
    RegWsh.RegWrite hkey_root+hkey_path+hkey_key,""
    hkey_key="/footer"
    RegWsh.RegWrite hkey_root+hkey_path+hkey_key,""
    end function
</script>
<script>
function pay_cancel(obj){
    var url =  $(obj).attr('data-url')+'/'+Math.random();
    layer.open({
        type: 2,
        title: '退款操作',
        shadeClose: true,
        shade: 0.8,
        area: ['45%', '50%'],
        content: url, 
    });
}
//取消付款
function pay_callback(s){
	if(s==1){
		layer.msg('操作成功', {icon: 1});
		layer.closeAll('iframe');
		location.href =	location.href;
	}else{
		layer.msg('操作失败', {icon: 3});
		layer.closeAll('iframe');
		location.href =	location.href;		
	}
}

// 弹出退换货商品
function selectGoods2(order_id){
	var url = "/index.php/home/Order/get_order_goods&order_id="+order_id;
	layer.open({
		type: 2,
		title: '选择商品',
		shadeClose: true,
		shade: 0.8,
		area: ['60%', '60%'],
		content: url, 
	});
}    
// 申请退换货
function call_back(order_id,goods_id)
{
	var url = "/index.php/home/Order/add_return_goods/order_id="+order_id+"&goods_id="+goods_id;	
	location.href = url;
}

$('.action').on('click', function(){
    var url = $(this).data('url');
        note = $('#note').val();
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {note: note},
        success: function(data){
            if( data.code ){
                location.href = location.href;
            }else{
                layer.msg('操作失败', {icon: 3});
            }
        }
    })    
})
</script>
</html>