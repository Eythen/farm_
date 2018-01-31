<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:82:"/Applications/MAMP/htdocs/farm/public/../application/home/view/book/orderlist.html";i:1516084571;s:78:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/css.html";i:1516084571;s:77:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/js.html";i:1516084571;}*/ ?>
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


    </style>
</head>
<body>
    <div class="panel panel-info well">
        <div class="panel-heading">
            <h4 class="panel-title">
                <b class="replyform">查询</b>
            </h4>
        </div>
        <div class="panel-body">
            <div class="col-sm-12">
                <form class="form-horizontal" id="mysearch">
                    <div class="form-group">
                        <div class="col-sm-4">
                           <label class="col-sm-4 control-label">座位：</label>
                           <div class="col-sm-7">
                               <select name="table" id="table" class="form-control">
                                   <option value="">全部</option> 
                                   <?php if(is_array($seat) || $seat instanceof \think\Collection || $seat instanceof \think\Paginator): $i = 0; $__LIST__ = $seat;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>                 
                                   <option value="<?php echo $key; ?>"><?php echo $vo; ?></option>
                                   <?php endforeach; endif; else: echo "" ;endif; ?>
                               </select>
                           </div>    
                       </div>             
                        <div class="col-sm-4">
                            <label class="col-sm-4 control-label">用户：</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="key_word" id="key_word" placeholder="请输入用户" value="">
                            </div>
                        </div> 
                        <div class="col-sm-4">
                            <label class="col-sm-4 control-label">手机：</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="mobile" id="mobile" placeholder="请输入手机" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                           <label class="col-sm-4 control-label">状态：</label>
                           <div class="col-sm-7">
                               <select name="order_status" id="order_status" class="form-control">
                                   <option value="">全部</option> 
                                   <?php if(is_array($order_status) || $order_status instanceof \think\Collection || $order_status instanceof \think\Paginator): $i = 0; $__LIST__ = $order_status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>                 
                                   <option value="<?php echo $key; ?>"><?php echo $vo; ?></option>
                                   <?php endforeach; endif; else: echo "" ;endif; ?>
                               </select>
                           </div>    
                       </div>  
                        <div class="col-sm-4">
                            <label class="col-sm-4 control-label">日期：</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="date" id="date" placeholder="请输入日期" value="" readonly>
                            </div>
                        </div>
                        <div class="col-sm-4">
                           <label class="col-sm-4 control-label">时间：</label>
                           <div class="col-sm-7">
                               <select name="hours" id="hours" class="form-control">
                                   <option value="">全部</option>            
                                   <option value="8">上午8点</option>
                                   <option value="14">下午2点</option>
                                   <option value="20">晚上8点</option>
                               </select>
                           </div>    
                       </div>             
                    </div>

                    

                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-3">
                            <button type="reset" class="col-sm-5 btn btn-danger">重置</button>
                            <span class="pull-right col-sm-5 btn btn-primary mysearch_btn">搜索</span>
                        </div> 
                          
                    </div>

                </form>    
            </div>
        </div>
    </div>

    <div class="well well-sm" >
        <div id="toolbar">
            <!-- <button id="add" class="btn btn-success">
                <i class="glyphicon glyphicon-plus"></i> 导入数据
            </button> -->
            <button id="more_del" class="btn btn-warning">
                <i class="glyphicon glyphicon-minus"></i> 批量删除
            </button>
            <!--<a href="<?php echo url('index/Goods/index'); ?>" target="_blank" id="myhome" class="btn btn-info">-->
                <!--<i class="glyphicon glyphicon-home"></i> 网站首页-->
            <!--</a>-->
            <!-- <button type="button" onclick="window.open('<?php echo url('wap/book/index'); ?>')" class="btn btn-primary"><i class="fa fa-plus"></i> 添加新订单</button> -->
            <button type="button" onclick="window.open('<?php echo url('tableStatus'); ?>')" class="btn btn-info"><i class="fa fa-plus"></i> 订桌状态</button>
            
        </div>
        <table class="table text-center table-hover table-condensed" data-toolbar="#toolbar" data-toggle="table" data-sort-name="id" data-sort-order="desc" data-url="<?php echo url('orderlist'); ?>" data-side-pagination="server" data-pagination="true" data-page-list="[10, 20, 50, 100, 200]" data-show-refresh="true" data-search="false" data-show-export="true" data-filter-control="true" data-filter-show-clear="true" >
            <thead>
                <tr>
                    <th data-halign="center" data-field="state" data-checkbox="true" ></th>
                    <th data-halign="center" data-field="order_id" data-filter-control="input" >ID</th>
                    <th data-halign="center" data-field="name" data-filter-control="input" >用户</th>
                    <th data-halign="center" data-field="add_time" data-filter-control="input">下单时间</th>
                    <th data-halign="center" data-field="at_time" data-filter-control="input">用餐时间</th>
                    <th data-halign="center" data-field="pay_status" data-filter-control="input">支付状态</th>
                    <th data-halign="center" data-field="order_status" data-filter-control="input">订单状态</th>

                    <th data-halign="center" data-field="operate" data-formatter="operateFormatter" data-events="operateEvents" >操作</th>
                </tr>
            </thead>
        </table>
    </div>
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
    <link href="__PUBLIC__/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <script src="__PUBLIC__/plugins/daterangepicker/moment.min.js" type="text/javascript"></script>
    <script src="__PUBLIC__/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
    <script type="text/javascript">
    /*<input type="text" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')"  onchange="ajaxUpdateField(this);" name="store_count" size="4" data-table="goods" data-id="<?php echo $list['goods_id']; ?>" value="<?php echo $list['store_count']; ?>"/>   
                    </td>
                    <td class="text-center">                        
                        <img width="20" height="20" src="__PUBLIC__/images/<?php if($list["is_on_sale"] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('goods','goods_id','<?php echo $list['goods_id']; ?>','is_on_sale',this)"/}
                    </td>*/
        //function viewFormatter(value, row, index) {
            
            /*if(value){

                return [
                        '<span class="glyphicon glyphicon-ok text-success" date-index='+index+'></span>'
                    ].join('');
            }
            else{
                
                return [
                        '<span class="glyphicon glyphicon-remove text-danger"></span>'
                    ].join('');
            }*/

        

        function operateFormatter(value, row, index) {
            
            return [
                '<a href="javascript:void(0);" class="btn-icon view btn btn-sm btn-info" title="查看">查看</a>'].join('');
                /*     '  <a href="javascript:void(0);" class="edit btn btn-sm btn-primary" title="编辑">编辑</a>', '  <a href="javascript:void(0);" class="view btn btn-sm btn-warning" title="查看">查看</a>', '  <a href="javascript:void(0);" class="success btn btn-sm btn-info" title="完成">完成</a>', '  <a href="javascript:void(0);" class="refund btn btn-sm btn-danger" title="退款">退款</a>'
                ].join('');*/
              
        }

        window.operateEvents = {
          //查看
           'click .view': function (e, value, row, index) {
               //var url = $(this).data('url');
               var url = "<?php echo url('editOrder'); ?>"+"?act=view&order_id="+row.order_id;
               window.open(url);
           },
           /* 

            // 编辑
            'click .edit': function (e, value, row, index) {
                var url = "<?php echo url('editOrder'); ?>"+"?act=edit&order_id="+row.order_id;
                location.href=url;

            },
            //完成
           'click .success': function (e, value, row, index) {
               //var url = $(this).data('url');
               var url = "<?php echo url('editOrder'); ?>"+"?act=success&order_id="+row.order_id;
               window.open(url);
           },

            // 退款
            'click .refund': function (e, value, row, index) {
                var url = "<?php echo url('editOrder'); ?>"+"?act=refund&order_id="+row.order_id;
                location.href=url;

            },*/

            // 删除
            /*'click .remove': function (e, value, row, index) {
                $.ajax({
                    url: "<?php echo url('delGoods'); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {id: row.goods_id},
                    success: function(data){
                        if( data.status == 1 ){
                            $(".table tr[data-index="+index+"]").remove();
                        }else{
                            layer.msg(data.msg, {icon: 2}); 
                        }

                    }
                })
            },*/
        };

        $(function(){
            $('.form_date').datetimepicker({
                language: 'zh-CN',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0
            });

            $('#date').daterangepicker({
                format:"YYYY/MM/DD",
                singleDatePicker: true,
                showDropdowns: true,
                minDate:'2017/01/01',
                maxDate:'2030/01/01',
                startDate:'<?=date("Y/m/d")?>',
                locale : {
                    applyLabel : '确定',
                    cancelLabel : '取消',
                    fromLabel : '起始时间',
                    toLabel : '结束时间',
                    customRangeLabel : '自定义',
                    daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
                    monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
                    firstDay : 1
                }
            });

            $(".mysearch_btn").click(function(event) {

                var search = $("#mysearch").serialize();
                var url = "<?php echo url('orderlist'); ?>"+"?"+search;
                $(".table").bootstrapTable('refresh', {
                    url: url
                });

            });

            //收缩表单
            $('.panel-heading').click(function(){
                $(".panel-body").toggle(1000);
                $(".panel-heading i").toggleClass("fa-chevron-up").toggleClass("fa-chevron-down");
            });

            $('#add').click(function(event) {
                layer.open({
                    title:'新增',
                    type: 2,
                    area: ['400px', '300px'],
                    fix: false, //不固定
                    maxmin: true,
                    content: "<?php echo url('moreAdd'); ?>",
                    end: function(){
                        $(".table").bootstrapTable('refresh');
                    }
                });
            });

            //更新
            $('.myrefresh').click(function(event) {
                $(".table").bootstrapTable('refresh');
            });

            //批量删除
            $("#more_del").click(function(event) {
              var obj = $(".table").bootstrapTable('getSelections');
              var str = '';
              for ( var o in obj) {
                str = str + obj[o].goods_id + ",";        
              };  


         

              if( str!='' ){
                //询问框
                layer.confirm('您确定要批量删除么？', {
                  btn: ['确定','取消'] //按钮
                }, function(index){
                    layer.close(index);

                    $.ajax({
                        url: "<?php echo url('delOrder'); ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: {id: str},
                        success: function(data){
                            if( data.status ){
                                
                                $(".table").bootstrapTable('refresh');
                            }else{
                                layer.msg(data.msg, {icon: 2}); 
                            }

                        }
                    })
                }, function(){
                  
                });

                
              }else{
                layer.alert('请选择需要删除的!');
              }
            });

            
            $('input[name=btSelectAll]').after(" 全选 ");

           
        })

    </script>
</body>
</html>