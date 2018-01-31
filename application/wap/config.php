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
   // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'tpmsg.html',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'tpmsg.html',
    //银行对应图片
    'bank' => [
    		'工商银行' => 'ICBC',
    		'光大银行' => 'CEB',
    		'建设银行' => 'CCB',
    		'交通银行' => 'BCM',
    		'民生银行' => 'CMBC',
    		'农村信用社' => 'RCU',
    		'农业银行' => 'ABC',
    		'招商银行' => 'CMB',
    		'中国银行' => 'BOC',
    		],
];
