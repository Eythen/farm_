(function(){
////////////////////////////////////////////////////////////////////////////////////////// 树状图
    $.fn.tree = function(options) {
        var c = $.extend({expand: '展开子菜单', shrink: '收起子菜单'}, options||{});
        $(this).find('li:has(ul)').addClass('parent_li').find(' > span').attr('title', c.shrink);
        $(this).find('li.parent_li > span').on('click',
        function(e) {
            var children = $(this).parent('li.parent_li').find(' > ul > li');
            if (children.is(":visible")) {
                children.hide('fast');
                $(this).attr('title', c.expand).find(' > i').addClass('glyphicon-plus-sign').removeClass('glyphicon-minus-sign')
            } else {
                children.show('fast');
                $(this).attr('title', c.shrink).find(' > i').addClass('glyphicon-minus-sign').removeClass('glyphicon-plus-sign')
            }
            e.stopPropagation()
        });
        return $(this)
    };
})();
////////////////////////////////////////////////////////////////////////////////////////// Ajax函数
function ajax(url, data, func, type) {
    func = typeof(data) == 'function' ? data: func;
    $.ajax({
        url: url,
        data: typeof(data) == 'object' ? data: {},
        type: typeof(type) == 'undefined' ? 'get': type,
        timeout: 30000,
        complete: function(obj, status) {
            if (status == 'timeout') {
                alert('请求超时')
            } else if (status == 'success') {
                if (func != undefined) {
                    var text = obj.responseText;
                    if (text.indexOf('{') == 0) { //如果是json数据
                        eval('var text=' + text); //转为json格式
                    }
                    func(text)
                }
            } else {
                alert('请求失败')
            }
        }
    })
}
////////////////////////////////////////////////////////////////////////////////////////// 检查电话号码
function checktel(phone, ele) {
    var mobile_phone = /^1[34578]{1}\d{9}$/;
    var home_phone = /^((0\d{2,3})-)(\d{7,8})?$/;
    if (mobile_phone.exec(phone) || home_phone.exec(phone)) {
        return true;
    } else {
        parent.layer.alert('电话不正确', {
          icon: 0,
        }, function(index){
              $(ele).focus().parent().addClass('has-error');
              parent.layer.close(index);
        });
        return false
    }
}
////////////////////////////////////////////////////////////////////////////////////////// 提交为空检测
var zlayer = 0;
function ajax_submit(url, func){
    var canAjax=true;
    $('form[method="post"]').submit(function(){
        var canSubmit=true;
        $(this).find('.notempty').each(function(){
            if($(this).val() == ''){
                $(this).focus().parent().addClass('has-error');
                canSubmit=false;
                return false
            }
            else{
                $(this).parent().removeClass('has-error');
            }
        });
        if(canSubmit && canAjax){
            canAjax=false;
            zlayer = layer.load();
            ajax(url, $(this).serializeArray(), function(response){
                canAjax=true;
                layer.close(zlayer);
                func(response)
            }, 'post')
        }
        return false
    }).find('.notempty').keyup(function(){
        if($(this).val() != ''){
            $(this).parent().removeClass('has-error');
        }
    });
}
//判断上传文件格式
function checkExt(filepath,fileext){
    var icon_file = '';
    switch(fileext){
        case 'jpg': case 'jpeg': case 'gif': case 'png':
            icon_file = filepath;
            break;
        case 'doc': case 'docx':
            icon_file = imgUrl + 'ext/doc.png';
            break;
        case 'xls': case 'xlsx':
            icon_file = imgUrl + 'ext/xls.png';
            break;
        case 'pdf':
            icon_file = imgUrl + 'ext/pdf.png';
            break;
        case 'rar': case 'zip':
            icon_file = imgUrl + 'ext/rar.png';
            break;
        case 'mp4': case 'avi': case 'rmvb': case 'mov':
            icon_file = imgUrl + 'ext/media.png';
            break;
        default:
            icon_file = imgUrl + 'ext/other.png';
            break;
    }
    return '<img src="'+icon_file+'" width="100%">';
}

/*
 * 上传图片 后台专用
 * @access  public
 * @null int 一次上传图片张图
 * @elementid string 上传成功后返回路径插入指定ID元素内
 * @path  string 指定上传保存文件夹,默认存在Public/upload/temp/目录
 * @callback string  回调函数(单张图片返回保存路径字符串，多张则为路径数组 )
 */
/*function GetUploadify(num,elementid,path,callback)
{     
  var upurl ='/index.php/home/Uploadify/upload?num='+num+'&input='+elementid+'&path='+path+'&func='+callback;
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
}*/

/**
 * Created by admin on 2015/9/21.
 */

/**
 *  Ajax通用提交表单
 *  @var  form表单的id属性值  form_id
 *  @var  提交地址 subbmit_url
 */

function post_form(form_id,subbmit_url){
    if(form_id == '' && subbmit_url == ''){
        alert('缺少必要参数');
        return false;
    }
    //  序列化表单值
    var data = $('#'+form_id).serialize();

    $.post(subbmit_url,data,function(result){
        var obj = $.parseJSON(result);
        if(obj.status == 0){
            alert(obj.msg);
            return false;
        }
        if(obj.status == 1){
            alert(obj.msg);
            if(obj.data.return_url){
                //  返回跳转链接
                location.href = obj.data.return_url;
            }else{
                //  刷新页面
                location.reload();
            }
            return;
        }
    })
}


/**
 * 删除
 * @returns {void}
 */
function del_fun(del_url)
{
    if(confirm("确定要删除吗?"))
        location.href = del_url;
}  


// 修改指定表的指定字段值
function changeTableVal(table,id_name,id_value,field,obj)
{
		var src = "";
		 if($(obj).attr('src').indexOf("cancel.png") > 0 )
		 {          
				src = '/public/images/yes.png';
				var value = 1;
				
		 }else{                    
				src = '/public/images/cancel.png';
				var value = 0;
		 }                                                 
		$.ajax({
				url:"/index.php/home/Index/changeTableVal/table/"+table+"/id_name/"+id_name+"/id_value/"+id_value+"/field/"+field+'/value/'+value,			
				success: function(data){									
					$(obj).attr('src',src);           
				}
		});		
}

// 修改指定表的排序字段
function updateSort(table,id_name,id_value,field,obj)
{		       
 		var value = $(obj).val();
		$.ajax({
				url:"/index.php/home/Index/changeTableVal/table/"+table+"/id_name/"+id_name+"/id_value/"+id_value+"/field/"+field+'/value/'+value,			
				success: function(data){									
					layer.msg('更新成功', {icon: 1});   
				}
		});		
}

    // 输入框失去焦点 ajax 保存
function ajaxUpdateField(obj){
         var table = $(obj).data('table');
         var id = $(obj).data('id');             
         var field = $(obj).attr('name').replace(/field_/ig,""); // 字段名字
         var value = $(obj).val();               
         $.ajax({
             type:'POST',
             data:{table:table,id:id, field:field,value:value}, 
             url:"/index.php/home/Goods/updateField",
             success:function(data){                     
                      layer.msg('修改成功', {icon: 1,time:1000});
             }        
         });             
}

