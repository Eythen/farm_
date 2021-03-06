<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:82:"/Applications/MAMP/htdocs/farm/public/../application/home/view/book/combolist.html";i:1516084571;s:78:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/css.html";i:1516084571;s:77:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/js.html";i:1516084571;}*/ ?>
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
                            <label class="col-sm-4 control-label">标题：</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="key_word" id="key_word" placeholder="请输入套餐标题" value="">
                            </div>
                        </div> 
                        <div class="col-sm-4">
                            <label class="col-sm-4 control-label">套餐价格：</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="shop_price" id="shop_price" placeholder="请输入套餐价格" value="">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="col-sm-4 control-label">上下架：</label>
                            <div class="col-sm-7">
                                <select name="is_on_sale" id="is_on_sale" class="form-control">
                                    <option value="">全部</option>                  
                                    <option value="1">上架</option>
                                    <option value="0">下架</option>
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
            <button type="button" onclick="location.href='<?php echo url('addEditCombo'); ?>'" class="btn btn-primary"><i class="fa fa-plus"></i> 添加新套餐</button>
            
        </div>
        <table class="table text-center table-hover table-condensed" data-toolbar="#toolbar" data-toggle="table" data-sort-name="id" data-sort-order="desc" data-url="<?php echo url('combolist'); ?>" data-side-pagination="server" data-pagination="true" data-page-list="[10, 20, 50, 100, 200]" data-show-refresh="true" data-search="false" data-show-export="true" data-filter-control="true" data-filter-show-clear="true" >
            <thead>
                <tr>
                    <th data-halign="center" data-field="state" data-checkbox="true" ></th>
                    <th data-halign="center" data-field="id" data-filter-control="input" >ID</th>
                    <th data-halign="center" data-field="goods_name" data-filter-control="input" >套餐</th>
                    <th data-halign="center" data-field="shop_price" data-filter-control="input">套餐价格</th>
                    <th data-halign="center" data-field="last_update_uid" data-filter-control="input">更新人员</th>
                    <th data-halign="center" data-field="last_update" data-filter-control="input">更新时间</th>
                    <th data-halign="center" data-field="is_on_sale" data-formatter="is_on_sale_Formatter" >上架</th>
                    <!--<th data-halign="center" data-field="is_new" data-formatter="is_new_Formatter" >新品</th>-->
                    <!-- <th data-halign="center" data-field="is_recommend" data-formatter="is_recommend_Formatter" >推荐</th> -->
                    <!--<th data-halign="center" data-field="is_hot"  data-formatter="is_hot_Formatter" >热卖</th>-->
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
    <script type="text/javascript">
    /*<input type="text" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')"  onchange="ajaxUpdateField(this);" name="store_count" size="4" data-table="goods" data-id="<?php echo $list['id']; ?>" value="<?php echo $list['store_count']; ?>"/>   
                    </td>
                    <td class="text-center">                        
                        <img width="20" height="20" src="__PUBLIC__/images/<?php if($list["is_on_sale"] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('goods','id','<?php echo $list['id']; ?>','is_on_sale',this)"/}
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

        function store_count_Formatter(value, row, index) {
            
            return '<input type="text" onkeyup="this.value=this.value.replace(/[^\\d.]/g,\'\')" onpaste="this.value=this.value.replace(/[^\\d.]/g,\'\')"  onchange="ajaxUpdateField(this);" name="store_count" size="4" data-table="goods" data-id="'+row.id+'" value="'+value+'"/>';   
        }
        function is_on_sale_Formatter(value, row, index) {
            if(value){
                var img = 'yes.png';
            }
            else{
                var img = 'cancel.png';
            }
            return '<img width="20" height="20" src="__PUBLIC__/images/'+img+'" onclick="changeTableVal(\'book_combo\',\'id\','+row.id+',\'is_on_sale\',this)"/>';
              
        }
        function is_new_Formatter(value, row, index) {
            if(value){
                var img = 'yes.png';
            }
            else{
                var img = 'cancel.png';
            }
            return '<img width="20" height="20" src="__PUBLIC__/images/'+img+'" onclick="changeTableVal(\'book_combo\',\'id\','+row.id+',\'is_new\',this)"/>';
              
        }
        function is_recommend_Formatter(value, row, index) {
            if(value){
                var img = 'yes.png';
            }
            else{
                var img = 'cancel.png';
            }
            return '<img width="20" height="20" src="__PUBLIC__/images/'+img+'" onclick="changeTableVal(\'book_combo\',\'id\','+row.id+',\'is_recommend\',this)"/>';
              
        }
        function is_hot_Formatter(value, row, index) {
            if(value){
                var img = 'yes.png';
            }
            else{
                var img = 'cancel.png';
            }
            return '<img width="20" height="20" src="__PUBLIC__/images/'+img+'" onclick="changeTableVal(\'book_combo\',\'id\','+row.id+',\'is_hot\',this)"/>';
              
        }

        function operateFormatter(value, row, index) {
            
            return [
                //'<a href="javascript:void(0);" class="btn-icon view btn btn-sm btn-info" title="查看">查看</a>',
                     '  <a href="javascript:void(0);" class="btn-icon edit btn btn-sm btn-primary" title="编辑">编辑</a>', '  <a href="javascript:void(0);" class="btn-icon remove btn btn-sm btn-warning" title="删除">删除</a>'
                ].join('');
              
        }

        window.operateEvents = {
            // 查看
//            'click .view': function (e, value, row, index) {
//                //var url = $(this).data('url');
//                var url = "<?php echo url('wap/Goods/show'); ?>"+"?id="+row.id;
//                window.open(url);
//            },
            /*'click .delImgCache': function (e, value, row, index) {

            },
            'click .delHtmlCache': function (e, value, row, index) {

            },*/
            // 编辑
            'click .edit': function (e, value, row, index) {
                var url = "<?php echo url('addEditCombo'); ?>"+"?id="+row.id;
                location.href=url;
            },

            // 删除
            'click .remove': function (e, value, row, index) {
                $.ajax({
                    url: "<?php echo url('delCombo'); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {id: row.id},
                    success: function(data){
                        if( data.status == 1 ){
                            $(".table tr[data-index="+index+"]").remove();
                        }else{
                            layer.msg(data.msg, {icon: 2}); 
                        }

                    }
                })
            },
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

            $(".mysearch_btn").click(function(event) {

                var search = $("#mysearch").serialize();
                var url = "<?php echo url('combolist'); ?>"+"?"+search;
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
                str = str + obj[o].id + ",";        
              };  


         

              if( str!='' ){
                //询问框
                layer.confirm('您确定要批量删除么？', {
                  btn: ['确定','取消'] //按钮
                }, function(index){
                    layer.close(index);

                    $.ajax({
                        url: "<?php echo url('delCombo'); ?>",
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