<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:54:"/Applications/MAMP/htdocs/farm/thinkphp/tpl/tpmsg.html";i:1516084572;}*/ ?>
<!DOCTYPE html >
<html>
    <head>
        <meta charset="UTF-8">
        <title>产品详情</title>
        <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport" />
        <meta content="yes" name="apple-mobile-web-app-capable" />
        <meta content="black" name="apple-mobile-web-app-status-bar-style" />
        <meta content="telephone=no" name="format-detection" />

        <!-- 引入YDUI样式 -->
        <link rel="stylesheet" href="__PUBLIC__/css/ydui.css" />
        <link rel="stylesheet" href="__PUBLIC__/font/iconfont.css" />
        <link rel="stylesheet" href="__PUBLIC__/css/animate.min.css" />
        <link rel="stylesheet" href="__PUBLIC__/css/style.css" />

        <style type="text/css">
        .system-bj{ background: #fff; height:  100vh }
            .system-message{margin: 2rem auto .5rem; width: 2rem; height: 2rem;}
            .system-message img{ width: 100%;}
            .jump{ text-align: center; color: #666;}
            .jump #wait{ color: red; }
            .success{ margin-top: 1rem;}
        </style>

        <!-- 引入YDUI自适应解决方案类库 -->
        <script src="__PUBLIC__/js/ydui.flexible.js"></script>

    </head>
    <body>
        <header class="navheader">
            <a class="icon-jiantou-copy" href="javascript:history.go(-1)"></a>
            <h1 class="mui-title">系统提醒</h1>
        </header>

        <section  class="animated bounceInLeft">
            <section class="g-flexview">
                <div class="system-bj">
                        <?php switch ($code) {case 1:?>
                        <div class="system-message">
                            <img src="__PUBLIC__/img/icogantanhao.png">
                        </div>
                        <div style="width: 6rem;height: 2rem;margin: 0 auto">
                            <p class="success" style="text-align:center;margin-top: .3rem;"><?php echo(strip_tags($msg));?></p>
                        </div>
                        <?php break;case 0:?>
                        <div class="system-message">
                            <img src="__PUBLIC__/img/icogantanhao-sb.png">
                        </div>
                        <div style="width: 6rem;height: 2rem;margin: 0 auto">
                            <p class="error" style="text-align:center;margin-top: .3rem;"><?php echo(strip_tags($msg));?></p>
                        </div>
                        <?php break;} ?>
	                <p style="margin-top: -1rem;" class="jump">页面自动<a id="href" href="<?php echo($url);?>">跳转</a>等待时间：<b id="wait"><?php echo($wait);?></b></p>
                </div>
            </section>
        </section>
        <script type="text/javascript">
            (function(){
                var wait = document.getElementById('wait'),
                    href = document.getElementById('href').href;
                var interval = setInterval(function(){
                    var time = --wait.innerHTML;
                    if(time <= 0) {
                        location.href = href;
                        clearInterval(interval);
                    };
                }, 1000);
            })();
        </script>
    </body>
</html>