<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:80:"/Applications/MAMP/htdocs/farm/public/../application/home/view/wechat/index.html";i:1516084571;s:91:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/zhaoshang_header.html";i:1516084571;s:77:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/js.html";i:1516084571;}*/ ?>
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
    <link href="__PUBLIC__/css/plugins/upload/uploadify.css" rel="stylesheet">
<link href="__PUBLIC__/css/style.min862f.css?v=4.1.0" rel="stylesheet">
<div class="wrapper wrapper-content">
  <!-- Content Header (Page header) -->
  
  
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-list"></i> 公众号列表
              <!-- <a href="<?php echo url('home/Wechat/add'); ?>"> -->
              <button type="button" class="btn btn-info" id="add">
                  <i class="ace-icon fa fa-plus bigger-110"></i>
              </button>
             <!--  </a> -->
              <!-- <a href="javascript:;" class="btn pull-right btn-default" data-url="http://www.tp-shop.cn/Doc/Index/article/id/178/developer/phper.html" onclick="get_help(this)"><i class="fa fa-question-circle"></i> 帮助</a>              
              <a href="javascript:;" class="btn pull-right btn-default" data-url="http://www.tp-shop.cn/Doc/Index/article/id/283/developer/phper.html" onclick="get_help(this)"><i class="fa fa-question-circle"></i>三级分销</a> -->
          </h3>
        </div>
        <div class="panel-body">

              <div class="col-sm-12">
                <table class="table" data-toggle="table" data-sort-name="id" data-sort-order="desc" data-url="<?php echo url('Wechat/index'); ?>" data-height="500" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false" data-show-refresh="false" data-show-columns="false" >
                    <thead>
                        <tr class="info" >
                          <th data-halign="center" data-field="state"></th>
                          <th data-halign="center" data-field="id">ID</th>
                          <th data-halign="center" data-field="wxname">公众号</th>
                          <th data-halign="center" data-field="create_time" >创建时间</th>
                          <th data-halign="center" data-field="operate" data-formatter="operateFormatter" data-events="operateEvents">操作</th>
                        </tr>
                    </thead>
                </table>
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
        // 删除操作
        function del(id,t)
        {
            if(!confirm('确定要删除吗?'))
                return false;

            location.href = $(t).data('href');
        }
</script>
<script type="text/javascript">
$(function(){
    // 打印运营部交接表
    $(".yunying_print").click(function(event) {
      var obj = $(".table").bootstrapTable('getSelections');
      var str = unable = '';
      for ( var o in obj) {
        str = str + obj[o].id + ",";        
      };     
      if( str!='' ){
        window.open("<?php echo url('Print/print_table'); ?>?temp=yunying_print&id="+str);
      }else{
        art.dialog.alert('请选择需要打印的客户!');
      }
    });
   // 打印客户表
    $(".customer_list_print").click(function(event) {
      var obj = $(".table").bootstrapTable('getSelections');
      var str = unable = '';
      for ( var o in obj) {
        str = str + obj[o].id + ",";        
      };     
      if( str!='' ){
        window.open("<?php echo url('Print/print_table'); ?>?temp=customer_list_print&id="+str);
      }else{
        art.dialog.alert('请选择需要打印的客户!');
      }
    });

    $('#add').click(function(){
      var url = "<?php echo url('home/Wechat/add'); ?>";
      window.location = url;
      /*layer.open({
        type: 2,
        title: '增加',
        shadeClose: true,
        shade: 0.8,
        area: ['96%', '90%'],
        content: url //iframe的url
      }); */

    });

});

function operateFormatter(value, row, index) {
    return [
        '<button type="button" class="btn btn-primary btn-xs" onclick="parameter('+row.id+')" >编辑</button>', '         <button type="button" class="btn btn-success btn-xs" onclick="parameter2('+row.id+')" >删除</button>'
    ].join('');
}
function parameter (id){
    var url="<?php echo url('setting'); ?>"+"?id="+id;
    window.location = url;
    //layer.open(url,{width:1200,lock:true,height:600,resize:true,title:'客户档案',close:function(){ $("button[name='refresh']").click();  } }, false);
    /*layer.open({
      type: 2,
      title: '编辑',
      shadeClose: true,
      shade: 0.8,
      area: ['96%', '90%'],
      content: url //iframe的url
    }); */
}
function parameter2 (id){
   /* var url="<?php echo url('Reply/reply_only'); ?>"+"?id="+id;
    art.dialog.open(url,{width:1200,lock:true,height:600,resize:true,title:'客户回访记录',close:function(){ $("button[name='refresh']").click();} }, false);*/
}

function hasFormatter(value,row){
  if(row.has_store==1)
    return '<span style="color:#449d44" >有</span>';
  else  if(row.has_store==2)
    return '<span style="color:#c9302c" >无</span>';
}

</script> 
</body>
</html>