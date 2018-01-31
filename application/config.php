<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


//设置未定义变量不提示
error_reporting(E_ERROR | E_WARNING | E_PARSE);

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'wap',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => true,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => true,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    //'view_replace_str'       => [],
    'view_replace_str'   => [
        '__ROOT__' => '/',
        '__ADDONS__' => BASE_PATH . '/addons',
        '__PUBLIC__' => BASE_PATH . '/public',
        '__STATIC__' => BASE_PATH . '/application/admin/static',
        '__IMG__'    => BASE_PATH . '/application/admin/static/images',
        '__CSS__'    => BASE_PATH . '/application/admin/static/css',
        '__JS__'     => BASE_PATH . '/application/admin/static/js',
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],


    //yangqing 20170210 start

    

    // md5加密密钥(有用于用户密码)
    'MD5_KEY' => '2EE72A9235ED92C40A3551AA07DED85D',   //不要改变，否则所有密码都会出错

    'AUTH_KEY' => '09BA6D7FC20774402EF41FFFEB2F50B4',

    // AUTH权限认证
    'AUTH_ON' => false, // 认证开关
    'AUTH_TYPE' => 1, // 认证方式，1为实时认证；2为登录认证。
    'AUTH_GROUP' => 'yq_auth_group', // 用户组数据表名
    'AUTH_GROUP_ACCESS' => 'yq_auth_group_access', // 用户-用户组关系表
    'AUTH_RULE' => 'yq_auth_rule', // 权限规则表
    'AUTH_USER' => 'yq_users', // 用户信息表


    //啊里大于短信分类
    'alidayu_cat' => ['1'=> '短信验通知', '2'=> '短信推广', '3'=> '短信验证码'],

    //应用插件分布
    // 'OUTPUT_ENCODE' =>  true, //页面压缩输出支持   配置了 没鸟用
    'PAYMENT_PLUGIN_PATH' =>  PLUGIN_PATH.'payment',
    'LOGIN_PLUGIN_PATH' =>  PLUGIN_PATH.'login',
    'SHIPPING_PLUGIN_PATH' => PLUGIN_PATH.'shipping',
    'FUNCTION_PLUGIN_PATH' => PLUGIN_PATH.'function',

    'ORDER_STATUS' => array(
        0 => '待确认',
        1 => '已确认',
        2 => '已收货',
        3 => '已取消',                
        4 => '已完成',//评价完
        5 => '已作废',
    ),
    'SHIPPING_STATUS' => array(
        0 => '未发货',
        1 => '已发货',
        2 => '部分发货'         
    ),
    'PAY_STATUS' => array(
        0 => '未支付',
        1 => '已支付',
    ),
    'SEX' => array(
        0 => '保密',
        1 => '男',
        2 => '女'
    ),
    'COUPON_TYPE' => array(
        0 => '面额模板',
        1 => '按用户发放',           
        2 => '注册发放',
        3 => '邀请发放',
        4 => '线下发放',
        5 => '红包',
        6 => '兑换券',
        7 => '折扣券',

    ),
    'PROM_TYPE' => array(
        0 => '默认',
        1 => '抢购',
        2 => '团购',
        3 => '优惠'           
    ),
    // 订单用户端显示状态
    'WAITPAY'=>' AND pay_status = 0 AND order_status = 0 AND pay_code !="cod" ', //订单查询状态 待支付
    'WAITSEND'=>' AND (pay_status=1 OR pay_code="cod") AND shipping_status !=1 AND order_status in(0,1) ', //订单查询状态 待发货
    'WAITRECEIVE'=>' AND shipping_status=1 AND order_status = 1 ', //订单查询状态 待收货    
    'WAITCCOMMENT'=> ' AND order_status=2 ', // 待评价 确认收货     //'FINISHED'=>'  AND order_status=1 ', //订单查询状态 已完成 
    'FINISH'=> ' AND order_status = 4 ', // 已完成
    'CANCEL'=> ' AND order_status = 3 ', // 已取消
    'CANCELLED'=> 'AND order_status = 5 ',//已作废
    
    'ORDER_STATUS_DESC' => array(
        'WAITPAY' => '待支付',
        'WAITSEND'=>'待发货',
        'WAITRECEIVE'=>'待收货',
        'WAITCCOMMENT'=> '待评价',
        'CANCEL'=> '已取消',
        'FINISH'=> '已完成', //
        'CANCELLED'=> '已作废'
    ),

    
    //订餐处理   0待处理，1已完成，2退订中3无效 4未到店消费（退款成功）
    'book_order_status'  => [
        '0' => '待处理',
        '1' => '已完成',
        '2' => '退订中',
        '3' => '无效',
        '4' => '未到店消费（退款成功）',
    ],

    'book_order_pay_status'  => [
        '0' => '等待支付',
        '1' => '全额支付',
        '2' => '支付20%',
        '3' => '已退款',
    ],
    //位置
    'seat' => [
                '1_D'=> '1厅1D号8人位', 
                '1_F'=>  '1厅1D号8人位',
                '2_A'=>  '1厅2A号12人位',
                '3_A'=>  '1厅3A号6人位',
                '3_B'=>  '1厅3B号6人位',
                '3_C'=>  '1厅3C号6人位',
                '3_D'=>  '1厅3D号6人位',
                '3_E'=>  '1厅3E号6人位',
                '3_F'=>  '1厅3F号6人位',
                '4_A'=>  '1厅4A号6人位',
                '4_B'=>  '1厅4B号6人位',
                '4_C'=>  '1厅4C号6人位',
                '4_D'=>  '1厅4D号6人位',
                '4_E'=>  '1厅4E号6人位',
                '4_F'=>  '1厅4F号6人位',
                ],

    //认处理   0认养中，1已完成，2交付过程 3无效4发货5退订完成
    'pig_order_status'  => [
        '0' => '认养中',
        '1' => '已完成',
        '2' => '退订中',
        '3' => '无效',
        '4' => '发货',
        '5' => '退订完成',

    ],

    'pig_order_consign'  => [
        '1' => '上门自提方式',
        '2' => '物流发货方式',
    ],

    'pig_order_pay_status'  => [
        '0' => '等待支付',
        '1' => '支付完成',
    ],

    //代理商申请更换业务员状态
    'agent_apply_status' => [
        '0' => '等待处理',
        '1' => '更换完成',
        '2' => '拒绝更换',
    ],
    //代理商订单支付状态
    'agent_order_pay_status' => [
        '0' => '等待支付',
        '1' => '支付完成',
    ],

    //代理商订单状态
    'agent_order_status' => [
        '0' => '等待处理',
        '1' => '完成',
        '2' => '无效',
        '3' => '有效',
    ],

    //代理商订单发货申请状态   '0等待处理', '1完成', '2确认', '3无效', '4取消', '5发货'
    'agent_order_apply_status' => [
        '0' => '等待处理',
        '1' => '完成',
        '2' => '确认',
        '3' => '无效',
        '4' => '取消',
        '5' => '发货',
    ],

    //yangqing 20170210 end
];
