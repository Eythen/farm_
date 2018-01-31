<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

define('BASE_PATH', substr($_SERVER['SCRIPT_NAME'], 0, -10));
define('ROOT_PATH', dirname(APP_PATH) . DIRECTORY_SEPARATOR);
define('REAL_PATH', __DIR__ . DIRECTORY_SEPARATOR );




define('PLUGIN_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'plugins'. DIRECTORY_SEPARATOR );
define('UPLOAD_PATH','public/upload/'); // 编辑器图片上传路径
//define('TPSHOP_CACHE_TIME',31104000); // TPshop 缓存时间  31104000
define('TPSHOP_CACHE_TIME',3); // TPshop 缓存时间  31104000
define('SITE_URL','http://'.$_SERVER['HTTP_HOST']); // 网站域名

//define('__PREFIX__',config('database.prefix')); // 数据库前缀



// 开启调试模式
define('APP_DEBUG',True);






// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
