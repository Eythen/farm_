<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"/Applications/MAMP/htdocs/farm/public/../application/home/view/login/index.html";i:1516250742;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cmn-Hans">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=3,minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui" />
        <title>登录</title>
        <link href="__PUBLIC__/skin/login/css/login.css" rel="stylesheet">
    </head>

    <body>
        <div class="loginbanner">
            <div class="bj">
                <div class="login-box">
                    <div class="login-left">
                        <div class="logo">
                            <img src="__PUBLIC__/skin/login/img/logo.png" alt="logo"/>
                        </div>
                    </div>
                    <div class="login-right">
                        <div class="login-text">
                            <h1>九月新农园后台管理系统</h1>
                            <h2>JIUYUE Management System</h2>
                        </div>
                        <div class="login-form clear">
                            <form action="" id="" method="post">
                                <div class="form-group clear">
                                    <label>手机号：</label>
                                    <input type="text" id="mobile" class="form-control" placeholder="输入手机号码" />
                                </div>
                                <div class="form-group clear">
                                    <label>密       码：</label>
                                    <input type="password" id="password" class="form-control" placeholder="输入密码" />
                                </div>
                                <div class="form-group clear">
                                    <label>验证码：</label>
                                    <input type="text" id="captcha" class="form-control2" placeholder="输入验证码" />
                                    <img id="captcha_img" alt="点击更换" src="<?php echo url('Login/verify_code'); ?>" title="点击图片刷新验证码" onclick="this.src+='?rand='+Math.random();"  class="m">
                                </div>
                                <div class="login-group">　　
                                    <button type="button" class="btn dark-green" id="submit_btn">登录</button>
                                    <input type="reset" class="btn yellow-color" value="重置"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="promptly">Copyright©长沙九月畜牧科技股份有限公司丨<a href="http://www.miitbeian.gov.cn/" title="备案号" target="_blank">ICP备案：湘ICP备16016061号</a></div>
            </div>
        </div>
    </body>
    <script src="__PUBLIC__/skin/js/jquery.min.js"></script>
    <script src="__PUBLIC__/js/plugins/layer/layer.js"></script>
    <script>
        $('#submit_btn').click(function(){
                var myReg = /^1\d{10}$/; //手机正则
                if($('#mobile').val() == ''){
                    layer.msg('手机还没填呢！');    
                    $('#mobile').focus();
                }else if(!myReg.test($('#mobile').val())){
                    layer.msg('您的手机格式错咯！');
                    $('#mobile').focus();
                }else if($('#password').val() == ''){
                    layer.msg('密码还没填呢！');
                    $('#password').focus();
                }else if($('#captcha').val() == ''){
                    layer.msg('验证码还没填呢！');
                    $('#captcha').focus();
                }else{
                    //ajax提交表单，#login_form为表单的ID。 如：$('#login_form').ajaxSubmit(function(data) { ... });
                    //layer.msg('登录成功咯！  正在为您跳转...','/');  
                    //
                    var tmp = Math.random();
                    var captcha = $("#captcha").val();
                    var mobile = $("#mobile").val();
                    var password = $("#password").val();
                    var index = layer.load(1);
                    $.ajax({
                       type: 'POST',
                        url:'<?php echo url("Login/index"); ?>',
                        //data: $('#login').serialize(),
                        data: {'mobile':mobile,'password':password,'captcha':captcha},
                        success:function(res){
                            if(res.status){
                                window.location.href = '<?php echo url("Index/index"); ?>';

                            }else{
                                $('#captcha_img').click();
                                $('#captcha').val('');
                                $('#captcha').focus();
                                layer.close(index);
                                layer.msg(res.msg);
                           }
                            
                            
                        },
                        error:function(){
                            layer.close(index);
                            $('#captcha_img').click();
                            $('#captcha').val('');
                            layer.msg('系统错误');
                        }
                    });
                
                    
                }
         
            })

            //回车登陆
            $(document).on('keypress',function(event){
                if(event.keyCode == "13"){
                    $('#submit_btn').click();
                    //$('#captcha').val('');
                    //$('#captcha_img').click();
                }
            })

    </script>


</html>