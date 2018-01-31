<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:84:"/Applications/MAMP/htdocs/farm/public/../application/home/view/book/_combogoods.html";i:1516084571;s:78:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/css.html";i:1516084571;s:85:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/min-header.html";i:1516084571;s:77:"/Applications/MAMP/htdocs/farm/public/../application/home/view/public/js.html";i:1516084571;}*/ ?>
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
  <!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>管理后台</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <!--<link href="__PUBLIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />-->
    <!-- FontAwesome 4.3.0 -->
 	<!--<link href="__PUBLIC__/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />-->
    <!-- Ionicons 2.0.0 --
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <!--<link href="__PUBLIC__/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />-->
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
    	folder instead of downloading all of them to reduce the load. -->
    <!--<link href="__PUBLIC__/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />-->
    <!-- iCheck -->
    <link href="__PUBLIC__/js/plugins/artDialog4.1.7/skins/blue.css" rel="stylesheet" type="text/css" />
    <!-- jQuery 2.1.4 -->
    <script src="__PUBLIC__/js/jquery-2.1.1.js"></script>
    <script src="__PUBLIC__/js/global.js"></script>
    <script src="__PUBLIC__/js/myFormValidate.js"></script>
    <script src="__PUBLIC__/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="__PUBLIC__/js/plugins/layer/layer.js"></script><!-- 弹窗js 参考文档 http://layer.layui.com/-->
    <script type="text/javascript">
    function delfunc(obj){
    	layer.confirm('确认删除？', {
    		  btn: ['确定','取消'] //按钮
    		}, function(){
   				$.ajax({
   					type : 'post',
   					url : $(obj).attr('data-url'),
   					data : {act:'del',del_id:$(obj).attr('data-id')},
   					dataType : 'json',
   					success : function(data){
   						if(data==1){
   							layer.msg('操作成功', {icon: 1});
   							$(obj).parent().parent().remove();
   						}else{
   							layer.msg(data, {icon: 2,time: 2000});
   						}
   						layer.closeAll();
   					}
   				})
    		}, function(index){
    			layer.close(index);
    			return false;// 取消
    		}
    	);
    }
    
    //全选
    function selectAll(name,obj){
    	$('input[name*='+name+']').prop('checked', $(obj).checked);
    }   
    
    function get_help(obj){
        layer.open({
            type: 2,
            title: '帮助手册',
            shadeClose: true,
            shade: 0.3,
            area: ['90%', '90%'],
            content: $(obj).attr('data-url'), 
        });
    }
    
    function delAll(obj,name){
    	var a = [];
    	$('input[name*='+name+']').each(function(i,o){
    		if($(o).is(':checked')){
    			a.push($(o).val());
    		}
    	})
    	if(a.length == 0){
    		layer.alert('请选择删除项', {icon: 2});
    		return;
    	}
    	layer.confirm('确认删除？', {btn: ['确定','取消'] }, function(){
    			$.ajax({
    				type : 'get',
    				url : $(obj).attr('data-url'),
    				data : {act:'del',del_id:a},
    				dataType : 'json',
    				success : function(data){
    					if(data == 1){
    						layer.msg('操作成功', {icon: 1});
    						$('input[name*='+name+']').each(function(i,o){
    							if($(o).is(':checked')){
    								$(o).parent().parent().remove();
    							}
    						})
    					}else{
    						layer.msg(data, {icon: 2,time: 2000});
    					}
    					layer.closeAll();
    				}
    			})
    		}, function(index){
    			layer.close(index);
    			return false;// 取消
    		}
    	);	
    }
    </script>        
  </head>
  <body style="background-color:#ecf0f5;">
 

  <style type="text/css">
        body{background-color: #eee}
    .btn-icon{ font-size: 12px; margin: 0px 5px; }
  </style>
</head>

<body>
<div class="wrapper animate col-md-12">
    <section class="content col-md-12">
        <!-- Main content -->
        <div class="container-fluid col-md-12">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i>返回</a>
              
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i><?php if($goodsInfo['goods_id'] == ''): ?>新增商品<?php else: ?>编辑商品<?php endif; ?></h3>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_tongyong" data-toggle="tab">通用信息</a></li>
<!--                        <li><a href="#tab_goods_desc" data-toggle="tab">描述信息</a></li>-->
                        <!-- <li><a href="#tab_goods_images" data-toggle="tab">商品相册</a></li> -->
                        <!--<li><a href="#tab_goods_spec" data-toggle="tab">商品规格</a></li>-->
                        <!--<li><a href="#tab_goods_attr" data-toggle="tab">商品属性</a></li>-->
                      <!--   <li><a href="#tab_goods_size" data-toggle="tab">商品参数</a></li>
                      <li><a href="#tab_goods_entry" data-toggle="tab">套餐组成</a></li> -->
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="addEditGoodsForm" >
                    
                        <!--通用信息-->
                    <div class="tab-content">                     
                        <div class="tab-pane active" id="tab_tongyong">
                           
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>商品名称:</td>
                                    <td>
                                        <input type="text" value="<?php echo $goodsInfo['goods_name']; ?>" name="goods_name" class="form-control" style="width:350px;"/>
                                        <span id="err_goods_name" style="color:#F00; display:none;"></span>                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td>商品简介:</td>
                                    <td>
                                      <textarea rows="3" cols="50" name="goods_remark"><?php echo $goodsInfo['goods_remark']; ?></textarea>
                                        <span id="err_goods_remark" style="color:#F00; display:none;"></span>
                                         
                                    </td>                                                                       
                                </tr>
                                <!-- <tr>
                                    <td>商品货号</td>
                                    <td>                                                                               
                                        <input type="text" value="<?php echo $goodsInfo['goods_sn']; ?>" name="goods_sn" class="form-control" style="width:350px;"/>
                                        <span id="err_goods_sn" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td>本店售价:</td>
                                    <td>
                                        <input type="text" value="<?php echo $goodsInfo['shop_price']; ?>" name="shop_price" class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                        <span id="err_shop_price" style="color:#F00; display:none;"></span>                                                 
                                    </td>
                                </tr>  
                                <tr>
                                    <td>上传商品图片:</td>
                                    <td>
                                        <input type="button" value="上传图片"  onclick="GetUploadify(1,'','book_goods','call_back');"/>
                                        <input type="text" class="input-sm"  name="original_img" id="original_img" value="<?php echo $goodsInfo['original_img']; ?>"/>
                                        <?php if($goodsInfo['original_img'] != null): ?>
                                            &nbsp;&nbsp;
                                            <a target="_blank" href="<?php echo $goodsInfo['original_img']; ?>" id="original_img2">
                                                <img width="25" height="25" src="/public/images/image_icon.jpg">
                                            </a>
                                        <?php endif; ?>
                                        <span id="err_original_img" style="color:#F00; display:none;"></span>                                                 
                                    </td>
                                </tr>                                 
                                </tbody>                                
                                </table>
                        </div>

                    </div>              
                    <div class="pull-right">
                        <input type="hidden" name="goods_id" value="<?php echo $goodsInfo['goods_id']; ?>">
                        <!-- onclick="ajax_submit_form('addEditGoodsForm','<?php echo url('Goods/addEditGoods?is_ajax=1'); ?>');"  -->
                        <button class="btn btn-primary save" title="" data-toggle="tooltip" type="button" data-original-title="保存">保存</button>
                    </div>
          </form><!--表单数据-->
                </div>
            </div>
        </div>    <!-- /.content -->
    </section>
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
     var editor;
    $(function () {
        

        $('.save').on('click', function(){
           $.ajax({
              url: "<?php echo url('addEditGoods'); ?>",
              type: 'POST',
              dataType: 'json',
              data: $('#addEditGoodsForm').serialize(),
              success: function(data){
                  if( data.code ){
                      window.location.href = "<?php echo url('goodslist'); ?>";
                      //window.location.reload();
                  }else{
                      layer.msg(data.msg, {icon: 2}); 
                  }

              }
          });

        });

    });  
    
    
	$('#publish_time').daterangepicker({
		format:"YYYY-MM-DD",
		singleDatePicker: true,
		showDropdowns: true,
		minDate:'2016-01-01',
		maxDate:'2030-01-01',
		startDate:'<?php echo date("Y-m-d",$info['publish_time']); ?>',
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
	
/*	function checkForm(){
		if($('input[name="title"]').val() == ''){
			alert("请填写文章标题！");
			return false;
		}
		if($('#cat_id').val() == '' || $('#cat_id').val() == 0){
			alert("请选择文章类别！");
			return false;
		}
		if($('#post_content').val() == ''){
			alert("请填写文章内容！");
			return false;
		}
		$('#add_post').submit();
	}*/

    /*
     * 以下是图片上传方法
     */
    // 上传商品图片成功回调函数
//    function call_back(fileurl_tmp){
//        $("#original_img").val(fileurl_tmp);
//        $("#original_img2").attr('href', fileurl_tmp);
//    }

    function call_back3(fileurl_tmp){
        $("#entry_img").val(fileurl_tmp);
        $("#entry_img2").attr('href', fileurl_tmp);
    }

 
    // 上传商品相册回调函数
    function call_back2(paths){
        var  last_div = $(".goods_xc:last").prop("outerHTML");  
        for (i=0;i<paths.length ;i++ )
        {                    
            $(".goods_xc:eq(0)").before(last_div);  // 插入一个 新图片
                $(".goods_xc:eq(0)").find('a:eq(0)').attr('href',paths[i]).attr('onclick','').attr('target', "_blank");// 修改他的链接地址
            $(".goods_xc:eq(0)").find('img').attr('src',paths[i]);// 修改他的图片路径
                $(".goods_xc:eq(0)").find('a:eq(1)').attr('onclick',"ClearPicArr2(this,'"+paths[i]+"')").text('删除');
            $(".goods_xc:eq(0)").find('input').val(paths[i]); // 设置隐藏域 要提交的值
        }          
    }
    /*
     * 上传之后删除组图input     
     * @access   public
     * @val      string  删除的图片input
     */
    function ClearPicArr2(obj,path)
    {
      $.ajax({
                    type:'GET',
                    url:"<?php echo url('home/Uploadify/delupload'); ?>",
                    data:{action:"del", filename:path},
                    success:function(){
                           $(obj).parent().remove(); // 删除完服务器的, 再删除 html上的图片        
                    }
    });
    // 删除数据库记录
      $.ajax({
                    type:'GET',
                    url:"<?php echo url('home/Goods/del_goods_images'); ?>",
                    data:{filename:path},
                    success:function(){
                          //     
                    }
    });   
    }
 


/** 以下 商品属性相关 js*/
$(document).ready(function(){
  
    // 商品类型切换时 ajax 调用  返回不同的属性输入框
    $("#goods_type").change(function(){        
        var goods_id = $("input[name='goods_id']").val();
        var type_id = $(this).val();
            $.ajax({
                    type:'GET',
                    data:{goods_id:goods_id,type_id:type_id}, 
                    url:"/index.php/home/Goods/ajaxGetAttrInput",
                    success:function(data){                            
                            $("#goods_attr_table tr:gt(0)").remove()
                            $("#goods_attr_table").append(data);
                    }        
            });                     
    });
  // 触发商品类型
  $("#goods_type").trigger('change');
    $("input[name='exchange_integral']").blur(function(){
        var shop_price = parseInt($("input[name='shop_price']").val());
        var exchange_integral = parseInt($(this).val());
        if (shop_price * 100 < exchange_integral) {

        }
    });
});
 

// 属性输入框的加减事件
function addAttr(a)
{
  var attr = $(a).parent().parent().prop("outerHTML");  
  attr = attr.replace('addAttr','delAttr').replace('+','-');  
  $(a).parent().parent().after(attr);
}
// 属性输入框的加减事件
function delAttr(a)
{
   $(a).parent().parent().remove();
}
 

/** 以下 商品规格相关 js*/
$(document).ready(function(){
  
    // 商品类型切换时 ajax 调用  返回不同的属性输入框
    $("#spec_type").change(function(){        
        var goods_id = '<?php echo $goodsInfo['goods_id']; ?>';
        var spec_type = $(this).val();
            $.ajax({
                    type:'GET',
                    data:{goods_id:goods_id,spec_type:spec_type}, 
                    url:"<?php echo url('home/Goods/ajaxGetSpecSelect'); ?>",
                    success:function(data){
                        $("#ajax_spec_data").html('')
                        $("#ajax_spec_data").append(data);
               //alert('132');
               ajaxGetSpecInput();  // 触发完  马上处罚 规格输入框
                    }
            });                     
    });
  // 触发商品规格
  $("#spec_type").trigger('change'); 
});

///** 以下是编辑时默认选中某个商品分类*/
//$(document).ready(function(){
//
//  <?php if($level_cat['2'] > 0): ?>
//     // 商品分类第二个下拉菜单
//     get_category('<?php echo $level_cat[1]; ?>','cat_id_2','<?php echo $level_cat[2]; ?>');
//  <?php endif; ?>
//  <?php if($level_cat['3'] > 0): ?>
//    // 商品分类第二个下拉菜单
//     get_category('<?php echo $level_cat[2]; ?>','cat_id_3','<?php echo $level_cat[3]; ?>');
//  <?php endif; ?>
//
//    //  扩展分类
//  <?php if($level_cat2['2'] > 0): ?>
//     // 商品分类第二个下拉菜单
//     get_category('<?php echo $level_cat2[1]; ?>','extend_cat_id_2','<?php echo $level_cat2[2]; ?>');
//  <?php endif; ?>
//  <?php if($level_cat2['3'] > 0): ?>
//    // 商品分类第二个下拉菜单
//     get_category('<?php echo $level_cat2[2]; ?>','extend_cat_id_3','<?php echo $level_cat2[3]; ?>');
//  <?php endif; ?>
//
//});

/*
 * 上传图片 后台专用
 * @access  public
 * @null int 一次上传图片张图
 * @elementid string 上传成功后返回路径插入指定ID元素内
 * @path  string 指定上传保存文件夹,默认存在Public/upload/temp/目录
 * @callback string  回调函数(单张图片返回保存路径字符串，多张则为路径数组 )
 */
function GetUploadify(num,elementid,path,callback)
{
  var upurl ='/index.php/home/Uploadify/upload?path='+path+'&func='+callback+'&num='+num+'&input='+elementid;
  var iframe_str='<iframe frameborder="0" ';
  iframe_str=iframe_str+'id=uploadify ';      
  iframe_str=iframe_str+' src='+upurl;
  iframe_str=iframe_str+' allowtransparency="true" class="uploadframe" scrolling="no"> ';
  iframe_str=iframe_str+'</iframe>';
  $("body").append(iframe_str); 
  $("iframe.uploadframe").css("height",$(document).height()).css("width","100%").css("position","absolute").css("left","0px").css("top","0px").css("z-index","999999").show();
  $(window).resize(function(){
    $("iframe.uploadframe").css("height",$(document).height()).show();
  });

}

function addSizeTr() {
    var size_num = $("#size_num").val()==''?1:$("#size_num").val();
    size_num++;
    var html = '<tr id="goods_size_'+size_num+'"><td onclick="delTr('+size_num+',\'goods_size\')"><i class="fa fa-minus"></i></td>'+
            '<td><input type="hidden" name="size['+size_num+'][id]" value=""><input name="size['+size_num+'][name]"></td>'+
            '<td><input name="size['+size_num+'][val]"></td>'+
            '</tr>';
    $("#goods_size").append(html);
    $("#size_num").val(size_num);
}

 function addEntryTr() {
     var entry_num = $("#entry_num").val()==''?1:$("#entry_num").val();
     entry_num++;
     var html = '<tr id="goods_entry_'+entry_num+'"><td onclick="delTr('+entry_num+',\'goods_entry\')"><i class="fa fa-minus"></i></td>'+
             '<td>' +
             '<input type="button" value="上传图片"  onclick="GetUploadify(1,\'entry_img_'+entry_num+'\',\'goods\',\'\');"/>'+
             '<input type="text" class="input-sm"  name="entry['+entry_num+'][pic]" id="entry_img_'+entry_num+'" />' +
             '</td>'+
             '<td><input type="hidden" name="entry['+entry_num+'][id]" value=""><input name="entry['+entry_num+'][name]"></td>'+
             '<td><input name="entry['+entry_num+'][price]"></td>'+
             '<td><input name="entry['+entry_num+'][weight]"></td>'+
             '</tr>';
     $("#goods_entry").append(html);
     $("#entry_num").val(entry_num);
 }

function delTr(num,type,id) {
    if(id){
        $.ajax({
            type : "POST",
            dataType: 'json',
            url:"/index.php/home/Goods/delSizeFrom",
            data : {"id":id,"type":type},
            success: function(data){
                if(data.code == 1){
                    $("#"+type+'_'+num).remove();
                }else{
                    alert(data.msg);
                }
            }
        });
    }else{
        $("#"+type+'_'+num).remove();
    }
}
</script>
</body>
</html>