<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"/Applications/MAMP/htdocs/farm/public/../application/home/view/index/index.html";i:1516250742;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>九月新农园后台管理 - 主页</title>
    <meta name="keywords" content="客户管理CRM">
    <meta name="description" content="客户管理CRM">
    <link rel="shortcut icon" href="__PUBLIC__/img/favicon.ico">

    <link href="__PUBLIC__/css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
    <link href="__PUBLIC__/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="__PUBLIC__/css/animate.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/style.min.css?v=4.0.0" rel="stylesheet">
    <script type="text/javascript">
        if(window.top!==window.self){window.top.location=window.location};
    </script>
    <style type="text/css">
    .layui-layer-record{border-radius:10px!important;}
    .layui-layer-content {border-radius: 15px;}
    .skin-1 .minimalize-styl-2{margin:0;}
    .skin-1 .nav-header {background: #516774;}
    .navbar-static-side {
    background: #f5f5f5;
}
.skin-1 .nav>li>a {
    color: #555;
}
.skin-1 .nav>li.active {
    background: #f5f5f5;
}
.skin-1 .nav>li.active>a {
    color: #555;
    background-color: #ddd;
}
.nav>li>a .fa {
    color: #007AFF;
    font-weight: 400;
}
    </style>
</head>
<body class="fixed-sidebar full-height-layout gray-bg skin-1" style="overflow:hidden">
    <div id="wrapper">
        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <div class="logobox">
                                <!--LOGO-->
                                <img src="__PUBLIC__/img/menu/info_left_top_logo.png" />
                            </div>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="namebox" style="left:90px;position: relative;">
                                   <span style="position: absolute;left:-40px;top:0;" >
                                    <!--头像-->
                                    <img alt="image" id="avatar" height="30px" class="img-circle" src="<?php echo session('avatar'); ?>" />
                                  </span>
                                <span class="block"><strong class="font-bold"><?php echo session('uname'); ?></strong></span>
                                <span class="text-muted text-xs block"><?php echo session('dept_name'); ?><b class="caret"></b></span>
                                </span>
                            </a>
                            <ul class="dropdown-menu animated fadeInDown m-t-xs">
<!--                                 <li><a class="J_menuItem" href="<?php echo url('Users/form_avatar'); ?>" id="editavatar">修改头像</a>
                                </li>
                                <li><a class="J_menuItem" href="profile.html">个人资料</a>
                                </li>
                                <li><a class="J_menuItem" href="contacts.html">联系我们</a>
                                </li>
                                <li><a class="J_menuItem" href="mailbox.html">信箱</a>
                                </li>-->
                                <li class="change_password"><a href="javascript:void(0)">修改密码</a></li> 
                                <li class="divider"></li> 
                                <li><a href="<?php echo url('Login/loginout'); ?>">安全退出</a>
                                </li>
                            </ul>
                        </div>
                        <div class="logo-element">
                            <img src="__PUBLIC__/img/menu/info_small_left_top_logo.png" />
                        </div>
                    </li>
                    <li>
                        <a class="J_menuItem" href="<?php echo url('main'); ?>" data-index="0" data-title="首页">
                            <i class="fa fa-home"></i>
                            <span class="nav-label">首页</span>
                        </a>
                    </li>

                    <?php if(!(empty($sideMenu) || (($sideMenu instanceof \think\Collection || $sideMenu instanceof \think\Paginator ) && $sideMenu->isEmpty()))): if(is_array($sideMenu) || $sideMenu instanceof \think\Collection || $sideMenu instanceof \think\Paginator): if( count($sideMenu)==0 ) : echo "" ;else: foreach($sideMenu as $key=>$v): ?>
                            <li id="menu_<?php echo $v['id']; ?>" data-menu="<?php echo $v['id']; ?>">
                                <?php if(empty($v['class2']) || (($v['class2'] instanceof \think\Collection || $v['class2'] instanceof \think\Paginator ) && $v['class2']->isEmpty())): ?>
                                    <a class="J_menuItem" href="<?php echo url($v['url']); ?>" data-title="<?php echo $v['title']; ?>">
                                        <i class="fa fa-<?php echo $v['icon']; ?>"></i>
                                        <span class="nav-label"><?php echo $v['title']; ?></span>
                                        <span class="label label-danger pull-right" id="remind_<?php echo $v['id']; ?>"></span>
                                    </a>
                                <?php else: ?>
                                    <a href="#">
                                        <i class="fa fa-<?php echo $v['icon']; ?>"></i>
                                        <span class="nav-label"><?php echo $v['title']; ?></span>
                                        <span class="label label-danger pull-right" id="remind_<?php echo $v['id']; ?>"></span>
                                        <span class="fa arrow"></span>
                                    </a>
                                    <ul class="nav nav-second-level">
                                        <?php if(is_array($v['class2']) || $v['class2'] instanceof \think\Collection || $v['class2'] instanceof \think\Paginator): if( count($v['class2'])==0 ) : echo "" ;else: foreach($v['class2'] as $key=>$v2): ?>
                                            <li id="menu_<?php echo $v2['id']; ?>" data-menu="<?php echo $v2['id']; ?>">
                                                <?php if(empty($v2['class3']) || (($v2['class3'] instanceof \think\Collection || $v2['class3'] instanceof \think\Paginator ) && $v2['class3']->isEmpty())): 

                                                    $urr = explode('/', $v2['url']);
                                                    $num = count($urr);

                                                    if( $num>3 ){
                                                        $url = implode('/',array_slice($urr, 0, 3));

                                                        $parm = array_slice($urr,3);
                                                        $parm = array_chunk($parm,2);
                                                        foreach($parm as $k => $v){
                                                            $p = $v[0]."=".$v[1]."&";
                                                        }
                                                        $p = rtrim($p, '&');
                                                        $url = url($url, $p);

                                                    }
                                                    else{
                                                        $url = url($v2['url']);
                                                    }

                                                ?>
 
                                                    <a class="J_menuItem" href="<?php echo $url; ?>" data-title="<?php echo $v2['title']; ?>"><?php echo $v2['title']; ?>
                                                        <span class="label label-danger pull-right" id="remind_<?php echo $v2['id']; ?>"></span></a>
                                                    <?php else: ?>
                                                    <a href="#"><?php echo $v2['title']; ?>
                                                        <span class="label label-danger pull-right" id="remind_<?php echo $v2['id']; ?>"></span>
                                                        <span class="fa arrow"></span>
                                                    </a>
                                                    <ul class="nav nav-third-level">
                                                        <?php if(is_array($v2['class3']) || $v2['class3'] instanceof \think\Collection || $v2['class3'] instanceof \think\Paginator): if( count($v2['class3'])==0 ) : echo "" ;else: foreach($v2['class3'] as $key=>$v3): ?>
                                                            <li id="menu_<?php echo $v3['id']; ?>" data-menu="<?php echo $v3['id']; ?>"><a class="J_menuItem" href="<?php echo url($v3['url']); ?>" data-title="<?php echo $v3['title']; ?>"><?php echo $v3['title']; ?><span class="label label-danger pull-right" id="remind_<?php echo $v3['id']; ?>"></span></a>
                                                            </li>
                                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; endif; else: echo "" ;endif; endif; ?>

                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg dashbard-1">
<!--             <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                        <form role="search" class="navbar-form-custom" method="post" action="search_results.html">
                            <div class="form-group">
                                <input id="search" type="text" placeholder="请输入您需要查找的内容 …" class="form-control" name="top-search" id="top-search">
                            </div>
                        </form>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li class="dropdown" id="bell-box">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="true">
                                <i class="fa fa-bell"></i> <span class="label label-warning" id="bell-remind"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-alerts">
                                <li>
                                    <a href="mailbox.html">
                                        <div>
                                            <i class="fa fa-envelope fa-fw"></i> 您有16条未读消息
                                            <span class="pull-right text-muted small">4分钟前</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="profile.html">
                                        <div>
                                            <i class="fa fa-qq fa-fw"></i> 3条新回复
                                            <span class="pull-right text-muted small">12分钟钱</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <div class="text-center link-block">
                                        <a class="J_menuItem" href="notifications.html" data-index="90">
                                            <strong>查看所有 </strong>
                                            <i class="fa fa-angle-right"></i>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div> -->
            <div class="row content-tabs">
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary roll-icon" href="#"><i class="fa fa-bars"></i> </a>
                <!-- <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
                </button> -->
                <nav class="page-tabs J_menuTabs">
                    <div class="page-tabs-content">
                        <a href="javascript:;" class="active J_menuTab" data-id="<?php echo url('main'); ?>">首页</a>
                        <a href="javascript:;" class="J_menuTab" data-id="<?php echo url('site/index'); ?>">系统设置</a>
                        <!-- <a href="javascript:;" class="active J_menuTab" data-id="<?php echo url('main'); ?>">首页</a> -->
                    </div>
                </nav>
                <!-- <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
                </button> -->
                <!-- <button class="roll-nav roll-right J_tabRight" style="width:120px;" id="iframe_refresh">刷新当前内容
                </button> -->

                <div class="roll-nav roll-right J_tabTips">
                <ul class="nav navbar-top-links navbar-right">
                    <li class="dropdown" id="bell-box">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="true">
                            <i class="fa fa-bell"></i> <span class="label label-warning" id="bell-remind"></span>
                        </a>
<!--                             <ul class="dropdown-menu dropdown-alerts">
                            <li>
                                <a href="mailbox.html">
                                    <div>
                                        <i class="fa fa-envelope fa-fw"></i> 您有16条未读消息
                                        <span class="pull-right text-muted small">4分钟前</span>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="profile.html">
                                    <div>
                                        <i class="fa fa-qq fa-fw"></i> 3条新回复
                                        <span class="pull-right text-muted small">12分钟钱</span>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <div class="text-center link-block">
                                    <a class="J_menuItem" href="notifications.html" data-index="90">
                                        <strong>查看所有 </strong>
                                        <i class="fa fa-angle-right"></i>
                                    </a>
                                </div>
                            </li>
                        </ul> -->
                    </li>
                </ul>
                </div>
                <!-- <div class="btn-group roll-nav roll-right">
                    <button class="dropdown J_tabClose" data-toggle="dropdown">关闭操作<span class="caret"></span>
                    </button>
                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
                        <li class="J_tabShowActive"><a>定位当前选项卡</a>
                        </li>
                        <li class="divider"></li>
                        <li class="J_tabCloseAll"><a>关闭全部选项卡</a>
                        </li>
                        <li class="J_tabCloseOther"><a>关闭其他选项卡</a>
                        </li>
                    </ul> 
                </div> -->
                <div class="btn-group roll-nav roll-right">
                    <a href="<?php echo url('@wap/index'); ?>" target="_blank" class=""><i class="fa fa fa-home"></i> 网站首页</a>
                </div>
                <a href="<?php echo url('Login/loginout'); ?>" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
            </div>
            <div class="row J_mainContent" id="content-main">
                <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="<?php echo url('main'); ?>" frameborder="0" data-id="<?php echo url('main'); ?>" seamless></iframe>
            </div>
            <div class="footer">
                <div class="pull-right">&copy;2016-2020版权所有 
                    
                    <!-- <img style="height:20px;margin-left:10px;margin-right: 10px;position: relative;top:-3px" src="__PUBLIC__/img/menu/bottom_logo.png" /> -->
                </div>
            </div>
        </div>
        <!--右侧部分结束-->
        <!--右侧边栏开始-->
        <!--右侧边栏结束-->
        <!--mini聊天窗口开始-->
        <div class="small-chat-box fadeInRight animated">
            <div class="heading" draggable="true">
                <small class="chat-date pull-right">
                    2015.9.1
                </small> 与 Beau-zihan 聊天中
            </div>
            <div class="content">
                <div class="left">
                    <div class="author-name">
                        Beau-zihan <small class="chat-date">
                        10:02
                    </small>
                    </div>
                    <div class="chat-message active">
                        你好
                    </div>
                </div>
                <div class="right">
                    <div class="author-name">
                        游客
                        <small class="chat-date">
                            11:24
                        </small>
                    </div>
                    <div class="chat-message">
                        你好，请问H+有帮助文档吗？
                    </div>
                </div>
                <div class="left">
                    <div class="author-name">
                        Beau-zihan
                        <small class="chat-date">
                            08:45
                        </small>
                    </div>
                    <div class="chat-message active">
                        有，购买的H+源码包中有帮助文档，位于docs文件夹下
                    </div>
                </div>
                <div class="right">
                    <div class="author-name">
                        游客
                        <small class="chat-date">
                            11:24
                        </small>
                    </div>
                    <div class="chat-message">
                        那除了帮助文档还提供什么样的服务？
                    </div>
                </div>
                <div class="left">
                    <div class="author-name">
                        Beau-zihan
                        <small class="chat-date">
                            08:45
                        </small>
                    </div>
                    <div class="chat-message active">
                        1.所有源码(未压缩、带注释版本)；
                        <br> 2.说明文档；
                        <br> 3.终身免费升级服务；
                        <br> 4.必要的技术支持；
                        <br> 5.付费二次开发服务；
                        <br> 6.授权许可；
                        <br> ……
                        <br>
                    </div>
                </div>
            </div>
            <div class="form-chat">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control"> <span class="input-group-btn"> <button
                        class="btn btn-primary" type="button">发送
                </button> </span>
                </div>
            </div>
        </div>
<!--         <div id="small-chat">
            <span class="badge badge-warning pull-right">5</span>
            <a class="open-small-chat">
                <i class="fa fa-comments"></i>
            </a>
        </div> -->
        
    </div>
    <script src="__PUBLIC__/js/jquery.min.js?v=1.9"></script>
    <script src="__PUBLIC__/js/bootstrap.min.js?v=3.3.5"></script>
    <script src="__PUBLIC__/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="__PUBLIC__/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="__PUBLIC__/js/plugins/layer/layer.js"></script>
    <script src="__PUBLIC__/js/hplus.min.js?v=4.0.0"></script>
    <script src="__PUBLIC__/js/contabs.min.js"></script>
    <script src="__PUBLIC__/js/plugins/pace/pace.min.js"></script>
    <script type="text/javascript">
    
    $(document).ready(function(){
        $('.change_password').click(function(){
            layer.open({
                title:'密码修改',
                type: 2,
                area: ['400px', '300px'],
                fix: false, //不固定
                maxmin: true,
                content: "<?php echo url('Admin/changePassword'); ?>",
                end: function(){
                    //$(".table").bootstrapTable('refresh');
                }
            });
        });

        $('#iframe_refresh').click(function(){
            //alert(123);
            //alert($('iframe[class=J_iframe]').attr('src'));
            $('iframe[class=J_iframe]').attr('src', $('iframe[class=J_iframe]').attr('src'));
        });

        //remind();
    });
    //提醒
    //setInterval(remind,1200000);    //20分钟更新一次
    function remind(){
        var ids = [];
        //审核提醒
        $('#menu_98 li').each(function(){
            ids.push($(this).data('menu'));
        });
        if ($('#menu_98').size() == 1) {
            ids.push(49);
            ids.push(98);
        }
        //实时提醒
        $('#menu_105 li').each(function(){
            ids.push($(this).data('menu'));
        });
        if ($('#menu_105').size() == 1) {
            ids.push(105);
        }
        if (ids.length == 0) {
            return;
        }
        var id = ids.join(',');
        $.ajax({
           type: 'POST',
            url: '<?php echo url('remind'); ?>',
           data: 'ids='+id,
        success:function(res){
            if (!res.status) {
                console.log(res.info);
                return;
            }
            for (var a in res.info){
                if (res.info[a] != 0) {
                    //左侧提醒
                    if ($('#remind_'+a).next('.arrow').size() == 1) {
                        $('#remind_'+a).next('.arrow').remove();
                    }
                    if ($('#remind_'+a).text() != res.info[a]){
                        $('#menu_'+a).data('remind',res.info[a]);
                        $('#remind_'+a).text(res.info[a]);
                    }
                    //右上角提醒
                    if (a == 999 && $('#bell-remind').data('remind') != res.info[a]) {
                        $('#bell-remind').data('remind',res.info[a]);
                        $('#bell-remind').text(res.info[a]);
                    }
                    var tips = '';
                    var msg  = '';
                    var title  = '';
                    switch(a){
                        case '102'://新客户审核
                            tips = '您有'+res.info[a]+'条新客户审核需要处理';
                            break;
                        case '103'://退盟审核
                            tips = '您有'+res.info[a]+'条退盟审核需要处理';
                            break;
                        case '104'://结业审核
                            tips = '您有'+res.info[a]+'条结业审核需要处理';
                            break;
                        case '106'://新合同
                            tips = '您有'+res.info[a]+'份新合同需要处理';
                            break;//过期合同
                        case '107':
                            tips = '您有'+res.info[a]+'份过期合同需要处理';
                            break;
                        case '108'://任务
                            tips = '您有'+res.info[a]+'条任务需要处理';
                            break;
                        case '109'://无实体客户
                            tips = '您有'+res.info[a]+'个无实体客户';
                            break;
                        case '110'://待处理客户回访
                            tips = '您有'+res.info[a]+'个客户回访需要处理';
                            break;
                        case '111'://需要联系的客户
                            tips = '您有'+res.info[a]+'个客户需要联系';
                            break;
                        case '112'://15天内未达3次有效回访提醒
                            tips = '您有'+res.info[a]+'个客户需要回访';
                            break;
                        case '113'://反馈客户
                            tips = '您有'+res.info[a]+'个反馈需要处理';
                            break;
                        case '126'://投诉客户
                            tips = '您有'+res.info[a]+'个投诉需要处理';
                            break;
                        case '127'://新店铺信息
                            tips = '您有'+res.info[a]+'间新店铺信息需要处理';
                            break;
                        case '128'://新合同4小时内致电提醒
                            tips = '您有'+res.info[a]+'份新合同需要致电联系';
                            break;
                    }
                    if (tips) {
                        if ($('.dropdown-alerts').size() == 0) {
                            $('#bell-box').append('<ul class="dropdown-menu dropdown-alerts"></ul>');
                        }
                        if ($('#bell_'+a).size()==0) {
                            msg ='<li id="bell_'+ a +'" data-remind="'+res.info[a]+'">';
                            msg +='<a class="J_menuItem" href="'+$('#menu_'+a).children('a').attr('href')+'" data-index="'+$('#menu_'+a).children('a').data('index')+'" data-title="'+$('#menu_'+a).children('a').data('title')+'">';
                            msg +='<div>';
                            msg +='<i class="fa fa-envelope fa-fw"></i><span>'+tips+'</span>';
                            msg +='</div>';
                            msg +='</a>';
                            msg +='</li>';
                            if ($('.dropdown-alerts').find('li').size() != 0) {
                                $('.dropdown-alerts').prepend('<li class="divider"></li>');
                            }
                            $('.dropdown-alerts').prepend(msg);
                        }else{
                            if ($('#bell_'+a).data('remind') != res.info[a]) {
                                $('#bell_'+a).data('remind',res.info[a]);
                                $('#bell_'+a).find('span').text(tips);
                            }
                        }
                    }
                }else{
                    //左侧提醒
                    if($('#remind_'+a).parent().parent().parent('#side-menu').size() == 1){
                        if ($('#remind_'+a).next('.arrow').size() == 0) {
                            $('#remind_'+a).after('<span class="fa arrow"></span>');
                        }
                    };
                    $('#remind_'+a).text('');
                    //右上角提醒
                    if (a == 999) {
                        $('#bell-remind').data('remind',0);
                        $('#bell-remind').text('');
                        $('.dropdown-alerts').remove();
                    }
                    if ($('#bell_'+a).size() == 1) {
                        $('#bell_'+a).next('.divider').remove();
                        $('#bell_'+a).remove();
                    }
                }
            }
        }
        });
    }
    </script>
</body>
</html>
