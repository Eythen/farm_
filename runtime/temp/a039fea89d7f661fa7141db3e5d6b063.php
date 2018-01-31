<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:79:"/Applications/MAMP/htdocs/farm/public/../application/home/view/wechat/news.html";i:1516084571;s:91:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/zhaoshang_header.html";i:1516084571;s:77:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/js.html";i:1516084571;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>信息平台</title>
    <meta name="keywords" content="信息平台">
    <meta name="description" content="信息平台">
    <link href="__PUBLIC__/img/favicon.ico" rel="shortcut icon" >
    <link href="__PUBLIC__/css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
    <link href="__PUBLIC__/css/bootstrap-theme.min.css?v=3.3.5" rel="stylesheet">
    <link href="__PUBLIC__/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="__PUBLIC__/css/animate.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/plugins/bootstraptable/bootstrap-table.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/plugins/upload/uploadify.css" rel="stylesheet"><link href="__PUBLIC__/css/style.min862f.css?v=4.1.0" rel="stylesheet">

<link href="__PUBLIC__/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
<script src="__PUBLIC__/plugins/daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="__PUBLIC__/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>

<div class="wrapper wrapper-content"> 
  <!-- Content Header (Page header) -->
  
  
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-list"></i> 推送列表 
             <a href="<?php echo url('home/Wechat/add_news'); ?>">
              <button type="button" class="btn btn-info">
                  <i class="ace-icon fa fa-plus bigger-110"></i>
              </button>
              </a>
			<a href="javascript:;" class="btn pull-right btn-default" data-url="http://www.tp-shop.cn/Doc/Index/article/id/178/developer/phper.html" onclick="get_help(this)"><i class="fa fa-question-circle"></i> 帮助</a>
		  </h3>
        </div>
        <div class="panel-body">
          <div id="ajax_return">
              <div class="table-responsive">
                  <table class="table text-center" data-toggle="table" data-sort-name="id" data-sort-order="desc" data-url="<?php echo url('Wechat/img'); ?>" data-height="500" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-refresh="true" data-show-columns="true" >
                      <thead>
                          <tr class="info" >
                            <th data-halign="center" data-field="state" data-checkbox="true" ></th>
                            <th data-halign="center" data-field="id" data-sortable="true" >ID</th>
                            <th data-halign="center" data-field="keyword">关键词</th>
                            <th data-halign="center" data-field="title" >标题</th>
                            <th data-halign="center" data-field="desc" >描述</th>
                          <!--   <th data-halign="center" data-field="pic" >封面图片</th> -->
                            <th data-halign="center" data-field="pic" data-formatter="picFormatter" >封面图片</th>
                            <th data-halign="center" data-field="operate" data-formatter="operateFormatter" data-events="operateEvents">操作</th>
                          </tr>
                      </thead>
                  </table>
              </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.row --> 
  </section>
  <!-- /.content --> 
</div>
<!-- /.content-wrapper --> 

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

<script>

function picFormatter(value, row, index){
    return [
        '<button class="btn btn-info btn-sm" type="button" onclick="preview(\''+row.pic+'\')">预览</button>'
    ]
}
function operateFormatter(value, row, index){
    return [
        '<button type="button" class="btn btn-success edit"><span class="glyphicon glyphicon-pencil" ></span> 编辑</button>','    <button type="button" class="btn btn-warning del"><span class="glyphicon glyphicon-minus" ></span> 删除</button>'
    ].join('');
}

window.operateEvents = {
    'click .edit': function (e, value, row, index) {
        var url ="<?php echo url('add_img'); ?>"+"?id="+row.id;
        window.location = url;
        //art.dialog.open(url,{width:600,lock:true,height:300,resize:true,title:'查看',close:function(){ $("button[name='refresh']").click(); } }, false);
       /* layer.open({
          type: 2,
          title: '编辑',
          shadeClose: true,
          shade: 0.8,
          //area: ['96%', '90%'],
          content: url, //iframe的url
          end:function(){ $("button[name='refresh']").click(); }
        });*/ 
    },
    'click .del': function (e, value, row, index) {
        var str = row.id;
        if( str!='' ){
            $.ajax({
                type: 'POST',
                url: "<?php echo url('deleteUser'); ?>",
                data: {id: str},
                dataType: 'JSON',
                async: false,
                success: function(data) {
                    if(data.status){
                        id = row.id;
                        $(".table").bootstrapTable('remove', {
                            field: 'id',
                            values: id
                        });
                    }else{
                        layer.alert(data.info);
                    }
                }
            });
        }
    },
}

$(".delete").click(function() {
  var obj = $(".table").bootstrapTable('getSelections');
  var str = '';
  for ( var o in obj) {
    str = str + obj[o].id + ",";
  };
  if( str!='' ){
    $.ajax({
      type: 'POST',
      url: "<?php echo url('del_img'); ?>",
      data: {id: str},
      dataType: 'JSON',
      async: false,
      success: function(data) {
        if(data.status){
          ids = $.map(obj, function (row) {
                        return row.id;
                        });
                    $(".table").bootstrapTable('remove', {
                        field: 'id',
                        values: ids
                    });
        }else{
          layer.alert(data.info);
        }
      }
    });
  }
});

$(".add").click(function() {
    var url= $(this).data('rel');
    //art.dialog.open(url,{width:1000,lock:true,height:400,resize:true,title:'查看',close:function(){ $("button[name='refresh']").click(); } }, false);
    layer.open({
        type: 2,
        title: '增加',
        shadeClose: true,
        shade: 0.8,
        area: ['96%', '90%'],
        content: url, //iframe的url
        end:function(){ $("button[name='refresh']").click(); }
      }); 

});
        // 删除操作
        function del(id,t)
        {
            if(!confirm('确定要删除吗?'))
                return false;

            location.href = $(t).data('href');
        }
        function preview(id){
            layer.open({
                type: 2,
                title: false,
                closeBtn: false,
                area: ['320px','500px'],
                skin: 'layui-layer-nobg', //没有背景色
                shadeClose: true,
                content: "/index.php/home/Wechat/preview/id/"+id,
            });
        }
</script> 
</body>
</html>