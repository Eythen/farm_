<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 管理员操作记录
 * @param $log_url 操作URL
 * @param $log_info 记录信息
 */
function adminLog($log_info){
    $add['log_time'] = time();
    $add['admin_id'] = session('uid');
    $add['log_info'] = $log_info;
    $add['log_ip'] = request()->ip();
    $add['log_url'] = request()->url(true);
    db('admin_log')->insert($add);
}
/**
 * [get_tree 递归获取树状结构的数据]
 * @param  [array] $data [所有数据]
 * @param  [int]   $pid  [父级ID]
 * @return [array] $arr  [树状结构的数据]
 */
function get_tree($data = array(), $pid = 0) {
	foreach ($data as $key => $val) {
		if ($val['parent_id'] != $pid || empty($val)) {
			continue;
		}

		foreach ($val as $k => $v) {
			$arr[$key][$k] = $v;
		}
		$arr[$key]['son'] = get_tree($data, $val['id']); //获取子级
	}
	return $arr;
}

/**
 * [get_son_id 递归获取所有子级ID]
 * @param  [array] $data [所有数据]
 * @param  [array] $pid  [父级ID]
 * @return [array] $pid  [所有子级ID]
 */
function get_son_id($data = array(), $pid = array(0)) {
	$start = count($pid); //开始时，有多少个ID
	foreach ($data as $val) {
		if (!in_array($val['parent_id'], $pid) || empty($val)) {
			continue;
		}

		$pid[] = (int) $val['id']; //获取ID
	}
	$pid = array_unique($pid); //删除重复值
	$end = count($pid); //结束时，有多少个ID
	//如果存在子级，那么继续查找子级
	return $end > $start ? get_son_id($data, $pid) : $pid;
}



/**
 * 电话号码隐藏中间4位
 */
function change_tel($str) {
    return substr($str, 0, 3) . '****' . substr($str, 7, 4);
}

/**
 * 身份证隐藏后4位
 */
function change_idcard($str) {
	return substr($str, 0, 14) . '****';
}

/**
 * 验证日期
 */
function is_date($date){
    $time = strtotime($date);
    if(!$time){
        return false;
    }
    return checkdate(date('m', $time), date('d', $time), date('Y', $time));
}

/**
 * get_date 当前时间
 */
function get_date(){
    return date('Y-m-d H:i:s');
}

/**
 * [format_date 格式化时间]
 * @param  [string] $date [需要格式化的时间]
 * @return [string]         [格式化后的时间]
 */
function format_date($date){
    if (!is_date($date)) {
        return $date;
    }
    $res = time() - strtotime($date);
    switch ($res) {
        case 0:
            return '刚刚';
            break;
        case $res <= 30:
            return '刚刚';
            break;
        case $res <= 3600:
            return round($res / 60) . '分钟前';
            break;
        case $res <= 86400:
            return round($res / 3600) . '小时前';
            break;
        case $res <= 2592000:
            return round($res / 86400) . '天前';
            break;
        case $res <= 31104000:
            return round($res / 2592000) . '个月前';
            break;
        default:
            return round($res / 31104000) . '年前';
            break;
    }
}

/**
 * [format_day 格式化日期]
 * @param  [string] $date [需要格式化的时间]
 * @return [string]         [格式化后的时间]
 */
function format_day($date){
    if (!is_date($date)) {
        return $date;
    }
    $dateday = date('Y-m-d',strtotime($date));
    $time = date('H:i',strtotime($date));
    switch ($dateday) {
        case $dateday == date('Y-m-d'):
            return '今天 '.$time;
            break;
        case $dateday == date('Y-m-d',strtotime('-1 day')):
            return '昨天 '.$time;
            break;
        case $dateday == date('Y-m-d',strtotime('-2 day')):
            return '前天 '.$time;
            break;
        default:
            return $dateday.' '.$time;
            break;
    }
}

/**
 * 判断文件类型
 */
function check_ext($file){
	$ext = strtolower(pathinfo($file)['extension']);
    switch($ext){
        case 'jpg': case 'jpeg': case 'gif': case 'png':
            $icon_file = $file;
            break;
        case 'doc': case 'docx':
            $icon_file = '/Public/img/ext/doc.png';
            break;
        case 'xls': case 'xlsx':
            $icon_file = '/Public/img/ext/xls.png';
            break;
        case 'pdf':
            $icon_file = '/Public/img/ext/pdf.png';
            break;
        case 'rar': case 'zip':
            $icon_file = '/Public/img/ext/rar.png';
            break;
        case 'mp4': case 'avi': case 'rmvb': case 'mov':
            $icon_file = '/Public/img/ext/media.png';
            break;
        default:
            $icon_file = '/Public/img/ext/other.png';
            break;
    }
	return $icon_file;
}

/**
* utf8 to gbk
*/
function luan_ma($str){
    return mb_detect_encoding($str, 'utf-8, gbk') == 'CP936' ? iconv('gbk' ,'utf-8', $str) : $str;
}

/**
 * [format_avatar 生成用户头像地址]
 * @return [type] [description]
 */
function format_avatar($file){
    // if (!$file) {
    //     return '/Public/img/icon-logo1.png';
    // }
    // $url = 'http://ehr.honey-lovely.cn/Public/images/'.$file;
    return $file;
}

/**
 * 按照某个字段重新排序数组
 * @param array $multi_array
 * @param string $sort_key
 * @param string $sort
 * @return array
 */
function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
    if(is_array($multi_array)){
        foreach ($multi_array as $row_array){
            if(is_array($row_array)){
                $key_array[] = $row_array[$sort_key];
            }else{
                return false;
            }
        }
    }else{
        return false;
    }
    array_multisort($key_array,$sort,$multi_array);
    return $multi_array;
}


/*
 * curl post 模拟提交数据函数
 */
function cpost($url, $data, $timeout = 10) {
    $curl = curl_init(); // 启动一个CURL会话
    $this_header = array(
        "content-type: application/x-www-form-urlencoded;charset=UTF-8",
    );
    //curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno：' . curl_error($curl); //捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据
}

/**
 * 登录验证加密/解密
 * @param string $string [DECODE：解密字符串 / ENCODE：用户ID]
 * @param string $operation 方法[DECODE / ENCODE]
 * @param string $appid 根据appid获取key
 * @param number $expiry 验证超时时间
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 60){
		$ckey_length = 4; // 随机密钥长度 取值 0-32;
		// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
		// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
		// 当此值为 0 时，则不产生随机密钥
		$key = $key ? $key : C('AUTH_KEY');

		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(str_replace(array('-','_'), array('+','/'), substr($string, $ckey_length))) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace(array('+','/','='), array('-','_',''), base64_encode($result));
		}
}

/*生成数字签名*/
function authSign($request,$config){
    $sign = '';
    if (is_array($request)
        && !empty($request['appid'])){
            ksort($request);
            while (list($key, $val) = each($request)) {
                if ($key == 'sign' || $val === '') {
                    continue;
                }
                $sign .= html_entity_decode($key, ENT_COMPAT);
                $sign .= html_entity_decode($val, ENT_COMPAT);
            }
            if ($sign) {
                $sign .= $config[$request['appid']]['secret'];
                $sign = md5($sign);
            }
    }
    return $sign;
}

/*
* 修改专员名
*/
function edit_zyName($name=''){
    $userInfo = D('Users')->findName($name);
    $region = $userInfo['filiale_id'];
    switch ($region) {
        case 1:  $color="#f00";$region='北分'; break;
        case 2:  $color="#00f";$region='成分'; break;
        case 3:  $color="#0f0";$region='南分'; break;
        default: $color="#333";$region='总部'; break;
    }
    return '<span style="color:'.$color.'">'.$region.'-'.$name.'</span>';
}


/**
 * 获取当前月的  所有的周一 周六
 */
function get_monday_saturday($month){
    $monday   = array(); //初始化周一
    $saturday = array(); //初始化周六
    $t = date('t', strtotime($month)); //当前月的总天数
    //循环获取周几
    for($i=1 ; $i<=$t ; $i++){
        $i = $i<10 ? "0$i" : $i; //补0
        $w = date('w', strtotime("$month-$i")); //获取当前日为周几
        switch($w){
            case 1 : $monday[]   = "$month-$i"; break; //记录当前月所有周一
            case 6 : $saturday[] = "$month-$i"; break; //记录当前月所有周六
        }
    }
    return array('monday' => $monday, 'saturday' => $saturday);
}

/*
 * curl get 模拟提交数据函数
 */
function cget($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回  ]
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $return = curl_exec($ch);
    curl_close($ch);
    return $return;
}

/**
 * [goto_back 刷新当前页面]
 */
function goto_back(){
    echo '<script type="text/javascript">window.location.href = document.referrer;</script>';
    exit;
}

/*//加密解密
 * 功能：加密解密
 *$str = 'abcdef';
 *$key = 'www.phpyii.com';
 *echo authcode($str,'ENCODE',$key,0); //加密
 *$str = '56f4yER1DI2WTzWMqsfPpS9hwyoJnFP2MpC8SOhRrxO7BOk';
 *echo authcode($str,'DECODE',$key,0); //解密 */
function okcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
        substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
    sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}



function delEmpty($v)   
{  
    if ($v==="" || $v==="0000-00-00 00:00:00" || $v==="1970-01-01 00:00:00")   //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
    {  
        return false;  
    }  
    return true;  
}

/**
 * [formatTime 时间数据格式化]
 * @param  [type] $data [description]
 */
function formatTime($data, $type='Y-m-d H:i:s') {
    if (empty($data)) {
        $data = NUll;
    }
    else{
        $data = date($type, $data);
    }

    return $data;
}

/**
 *   实现中文字串截取无乱码的方法
 */
/*function getSubstr($string, $start, $length) {
      if(mb_strlen($string,'utf-8')>$length){
          $str = mb_substr($string, $start, $length,'utf-8');
          return $str.'...';
      }else{
          return $string;
      }
}*/

/**
 * [getUsername 获取后台用户名字]
 * @param  [type] $uid [用户UID]
 * @return [type]      [名字]
 */
function get_admin_name($uid){
    $ur = model('Users')->getAll(); //获取全部用户
    $ur = array_column($ur, 'username', 'uid');

    return $ur[$uid];
}

/**
 * [find_username 获取前台用户名字  缓存五分钟的用户数据]
 * @param  [int] $user_id [用户UID]
 * @return [string]      [名字]
 */
function find_username($user_id){
    if(!cache('all_usersname')){
        $ur = db('users')->select(); //获取全部用户
        $ur = array_column($ur, 'nickname', 'user_id');
        cache('all_usersname', $ur, 300);
    }
    else{
        $ur = cache('all_usersname');
    }
    return $ur[$user_id];
}

/**
 * [nophone 加密手机号码]
 * @param  [string] $phone [用户UID]
 * @return [string]      [加密过的号码]
 */
function nophone($phone){
    $str = substr($phone, 0,3)."****".substr($phone, -4);
    return $str;
}

/**
 * [ulandFormart 取优惠券数字值]
 * @param  [string] $str [字符串]
 * @return [string]      [数字值]
 */
function ulandFormart($str){
    $uland = 0;
    $pattern = '/(\d.*?)元无条件券/';
    preg_match_all($pattern, $str, $matches);

    $uland = $matches[1][0];

    if(empty($uland)){
        $pattern = '/满(\d.*?)元减(\d.*?)元/';
        preg_match_all($pattern, $str, $matches);
        $uland = $matches[2][0];
    }

    if(empty($uland)){
        $uland = 0;
    }
    return $uland;

}




// 应用公共文件
/**
 * [logWrite 日志记录]
 */
function logWrite($string){
    $myfile = fopen(REAL_PATH."/log.txt", "a+") or die("Unable to open file!");
    $txt = $string."\n";
    fwrite($myfile, $txt);
    fclose($myfile);
}
/**
 * tpshop检验登陆
 * @param
 * @return bool
 */
function is_login(){
    if(isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0){
        return $_SESSION['admin_id'];
    }else{
        return false;
    }
}
/**
 * 获取用户信息
 * @param $user_id_or_name  用户id 邮箱 手机 第三方id
 * @param int $type  类型 0 user_id查找 1 邮箱查找 2 手机查找 3 第三方唯一标识查找 4 微信unionid查询
 * @param string $oauth  第三方来源
 * @return mixed
 */
function get_user_info($user_id_or_name,$type = 0,$oauth=''){
    $map = array();
    if($type == 0)
        $map['user_id'] = $user_id_or_name;
    if($type == 1)
        $map['email'] = $user_id_or_name;
    if($type == 2)
        $map['mobile'] = $user_id_or_name;
    if($type == 3){
        $map['openid'] = $user_id_or_name;
        $map['oauth'] = $oauth;
    }
    if($type == 4){
        $map['unionid'] = $user_id_or_name;
        $map['oauth'] = $oauth;
    }
    $user = db('users')->where($map)->find();
    return $user;
}

/**
 * 更新会员等级,折扣，消费总额
 * @param $user_id  用户ID
 * @return boolean
 */
function update_user_level($user_id){
    $level_info = db('user_level')->order('level_id')->select();
    $total_amount = db('order')->where("user_id=$user_id AND (order_status=2 or order_status=4)")->sum('order_amount');
    if($level_info){
        foreach($level_info as $k=>$v){
            if($total_amount > $v['amount']){
                $level = $level_info[$k]['level_id'];
                $discount = $level_info[$k]['discount']/100;
            }
        }
        $user = session('user');
        if(isset($level) && $level>$user['level']){
            $updata = array('level'=>$level,'discount'=>$discount,'total_amount'=>$total_amount);
            db('users')->where("user_id=$user_id")->update($updata);            
        }
    }
}

/**
 *  商品缩略图 给于标签调用 拿出商品表的 original_img 原始图来裁切出来的
 * @param type $goods_id  商品id
 * @param type $width     生成缩略图的宽度
 * @param type $height    生成缩略图的高度
 */
function goods_thum_images($goods_id,$width,$height){

     if(empty($goods_id))
         return '';
    //判断缩略图是否存在
    $path = "Public/upload/goods/thumb/$goods_id/";
    $goods_thumb_name ="goods_thumb_{$goods_id}_{$width}_{$height}";
  
    // 这个商品 已经生成过这个比例的图片就直接返回了
    if(file_exists($path.$goods_thumb_name.'.jpg'))  return '/'.$path.$goods_thumb_name.'.jpg'; 
    if(file_exists($path.$goods_thumb_name.'.jpeg')) return '/'.$path.$goods_thumb_name.'.jpeg'; 
    if(file_exists($path.$goods_thumb_name.'.gif'))  return '/'.$path.$goods_thumb_name.'.gif'; 
    if(file_exists($path.$goods_thumb_name.'.png'))  return '/'.$path.$goods_thumb_name.'.png'; 
        
    $original_img = db('Goods')->where("goods_id = $goods_id")->value('original_img');
    if(empty($original_img)) return '';
    
    $original_img = '.'.$original_img; // 相对路径
    if(!file_exists($original_img)) return '';

    /*$image = new \think\Image();
    $image->open($original_img);*/

    $image = \think\Image::open($original_img);
        
    $goods_thumb_name = $goods_thumb_name. '.'.$image->type();
    //生成缩略图
    if(!is_dir($path)) 
        mkdir($path,0777,true);
    
    //参考文章 http://www.mb5u.com/biancheng/php/php_84533.html  改动参考 http://www.thinkphp.cn/topic/13542.html
    $image->thumb($width, $height,2)->save($path.$goods_thumb_name,NULL,100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存
    
    //图片水印处理
    $water = tpCache('water');
    if($water['is_mark']==1){
        $imgresource = './'.$path.$goods_thumb_name;
        if($width>$water['mark_width'] && $height>$water['mark_height']){
            if($water['mark_type'] == 'img'){
                $image->open($imgresource)->water(".".$water['mark_img'],$water['sel'],$water['mark_degree'])->save($imgresource);
            }else{
                //检查字体文件是否存在
                if(file_exists('./zhjt.ttf')){
                    $image->open($imgresource)->text($water['mark_txt'],'./zhjt.ttf',20,'#000000',$water['sel'])->save($imgresource);
                }
            }
        }
    }
    return '/'.$path.$goods_thumb_name;
}

/**
 * 商品相册缩略图
 */
function get_sub_images($sub_img,$goods_id,$width,$height){
    //判断缩略图是否存在
    $path = "Public/upload/goods/thumb/$goods_id/";
    $goods_thumb_name ="goods_sub_thumb_{$sub_img['img_id']}_{$width}_{$height}";
    //这个缩略图 已经生成过这个比例的图片就直接返回了
    if(file_exists($path.$goods_thumb_name.'.jpg'))  return '/'.$path.$goods_thumb_name.'.jpg';
    if(file_exists($path.$goods_thumb_name.'.jpeg')) return '/'.$path.$goods_thumb_name.'.jpeg';
    if(file_exists($path.$goods_thumb_name.'.gif'))  return '/'.$path.$goods_thumb_name.'.gif';
    if(file_exists($path.$goods_thumb_name.'.png'))  return '/'.$path.$goods_thumb_name.'.png';
    
    $original_img = '.'.$sub_img['image_url']; //相对路径
    if(!file_exists($original_img)) return '';
    
    /*$image = new \Think\Image();
    $image->open($original_img);*/

    $image = \think\Image::open($original_img);
    
    $goods_thumb_name = $goods_thumb_name. '.'.$image->type();
    // 生成缩略图
    if(!is_dir($path))
        mkdir($path,777,true);
    $image->thumb($width, $height,2)->save($path.$goods_thumb_name,NULL,100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存
    return '/'.$path.$goods_thumb_name;
}

/**
 * 刷新商品库存, 如果商品有设置规格库存, 则商品总库存 等于 所有规格库存相加
 * @param type $goods_id  商品id
 */
function refresh_stock($goods_id){
    $count = db("SpecGoodsPrice")->where("goods_id = $goods_id")->count();
    if($count == 0) return false; // 没有使用规格方式 没必要更改总库存

    $store_count = db("SpecGoodsPrice")->where("goods_id = $goods_id")->sum('store_count');
    db("Goods")->where("goods_id = $goods_id")->update(array('store_count'=>$store_count)); // 更新商品的总库存
}

/**
 * 根据 order_goods 表扣除商品库存
 * @param type $order_id  订单id
 */
function minus_stock($order_id){
    $orderGoodsArr = db('OrderGoods')->where("order_id = $order_id")->select();
    foreach($orderGoodsArr as $key => $val)
    {
        // 有选择规格的商品
        if(!empty($val['spec_key']))
        {   // 先到规格表里面扣除数量 再重新刷新一个 这件商品的总数量
            db('SpecGoodsPrice')->where("goods_id = {$val['goods_id']} and `key` = '{$val['spec_key']}'")->setDec('store_count',$val['goods_num']);
            refresh_stock($val['goods_id']);
        }else{
            db('Goods')->where("goods_id = {$val['goods_id']}")->setDec('store_count',$val['goods_num']); // 直接扣除商品总数量
        }
        //更新活动商品购买量
        if($val['prom_type']==1 || $val['prom_type']==2){
            $prom = get_goods_promotion($val['goods_id']);
            if($prom['is_end']==0){
                $tb = $val['prom_type']==1 ? 'flash_sale' : 'group_buy';
                db($tb)->where("id=".$val['prom_id'])->setInc('buy_num',$val['goods_num']);
                db($tb)->where("id=".$val['prom_id'])->setInc('order_num');
            }
        }
    }
}

/**
 * 邮件发送
 * @param $to    接收人
 * @param string $subject   邮件标题
 * @param string $content   邮件内容(html模板渲染后的内容)
 * @throws Exception
 * @throws phpmailerException
 */
function send_email($to,$subject='',$content=''){
    require_once THINK_PATH.'Library/Vendor/phpmailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $config = tpCache('smtp');
    $mail->CharSet  = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    //调试输出格式
    //$mail->Debugoutput = 'html';
    //smtp服务器
    $mail->Host = $config['smtp_server'];
    //端口 - likely to be 25, 465 or 587
    $mail->Port = $config['smtp_port'];
    
    if($mail->Port === 465) $mail->SMTPSecure = 'ssl';// 使用安全协议
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //用户名
    $mail->Username = $config['smtp_user'];
    //密码
    $mail->Password = $config['smtp_pwd'];
    //Set who the message is to be sent from
    $mail->setFrom($config['smtp_user']);
    //回复地址
    //$mail->addReplyTo('replyto@example.com', 'First Last');
    //接收邮件方
    if(is_array($to)){
        foreach ($to as $v){
            $mail->addAddress($v);
        }
    }else{
        $mail->addAddress($to);
    }

    $mail->isHTML(true);// send as HTML
    //标题
    $mail->Subject = $subject;
    //HTML内容转换
    $mail->msgHTML($content);
    //Replace the plain text body with one created manually
    //$mail->AltBody = 'This is a plain-text message body';
    //添加附件
    //$mail->addAttachment('images/phpmailer_mini.png');
    //send the message, check for errors
    return $mail->send();
}

/**
 * 发送短信
 * @param $mobile  手机号码
 * @param $content  内容
 * @return bool

function sendSMS($mobile,$content)
{
    $config = F('sms','',TEMP_PATH);
    $http = $config['sms_url'];         //短信接口
    $uid = $config['sms_user'];         //用户账号
    $pwd = $config['sms_pwd'];          //密码
    $mobileids = $mobile;               //号码发送状态接收唯一编号
    $data = array
    (
        'uid'=>$uid,                    //用户账号
        'pwd'=>md5($pwd.$uid),          //MD5位32密码,密码和用户名拼接字符
        'mobile'=>$mobile,              //号码，以英文逗号隔开
        'content'=>$content,            //内容
        'mobileids'=>$mobileids,
    );
    //即时发送
    $res = httpRequest($http,'POST',$data);//POST方式提交
    $stat = strpos($res,'stat=100');
    if($stat){
        return true;
    }else{
        return false;
    }
}
 */
//    /**
//     * 发送短信
//     * @param $mobile  手机号码
//     * @param $code    验证码
//     * @return bool    短信发送成功返回true失败返回false
//     */
function sendSMS($mobile, $code)
{
    //时区设置：亚洲/上海
    date_default_timezone_set('Asia/Shanghai');
    //这个是你下面实例化的类
    vendor('Alidayu.TopClient');
    //这个是topClient 里面需要实例化一个类所以我们也要加载 不然会报错
    vendor('Alidayu.ResultSet');
    //这个是成功后返回的信息文件
    vendor('Alidayu.RequestCheckUtil');
    //这个是错误信息返回的一个php文件
    vendor('Alidayu.TopLogger');
    //这个也是你下面示例的类
    vendor('Alidayu.AlibabaAliqinFcSmsNumSendRequest');

    $c = new \TopClient;
    $config =  tpCache('sms');
    //短信内容：公司名/名牌名/产品名
    $product = $config['sms_product'];
    //App Key的值 这个在开发者控制台的应用管理点击你添加过的应用就有了
    $c->appkey = $config['sms_appkey'];
    //App Secret的值也是在哪里一起的 你点击查看就有了
    $c->secretKey = $config['sms_secretKey'];
    //这个是用户名记录那个用户操作
    $req = new \AlibabaAliqinFcSmsNumSendRequest;
    //代理人编号 可选
    $req->setExtend("123456");
    //短信类型 此处默认 不用修改
    $req->setSmsType("normal");
    //短信签名 必须
    $req->setSmsFreeSignName("注册验证");
    //短信模板 必须
    $req->setSmsParam("{\"code\":\"$code\",\"product\":\"$product\"}");
    //短信接收号码 支持单个或多个手机号码，传入号码为11位手机号码，不能加0或+86。群发短信需传入多个号码，以英文逗号分隔，
    $req->setRecNum("$mobile");
    //短信模板ID，传入的模板必须是在阿里大鱼“管理中心-短信模板管理”中的可用模板。
    $req->setSmsTemplateCode($config['sms_templateCode']); // templateCode
    
    $c->format='json'; 
    //发送短信
    $resp = $c->execute($req);
    //短信发送成功返回True，失败返回false
    //if (!$resp)
    if ($resp && $resp->result)   // if($resp->result->success == true)
    {
        // 从数据库中查询是否有验证码
        $data = db('sms_log')->where("code = '$code' and add_time > ".(time() - 60*60))->find();
        // 没有就插入验证码,供验证用
        empty($data) && db('sms_log')->add(array('mobile' => $mobile, 'code' => $code, 'add_time' => time(), 'session_id' => SESSION_ID));
        return true;        
    }
    else 
    {
        return false;
    }
}

/**
 * 查询快递
 * @param $postcom  快递公司编码
 * @param $getNu  快递单号
 * @return array  物流跟踪信息数组
 */
function queryExpress($postcom , $getNu) {
    $url = "http://wap.kuaidi100.com/wap_result.jsp?rand=".time()."&id={$postcom}&fromWeb=null&postid={$getNu}";
    //$resp = httpRequest($url,'GET');
    $resp = file_get_contents($url);
    if (empty($resp)) {
        return array('status'=>0, 'message'=>'物流公司网络异常，请稍后查询');
    }
    preg_match_all('/\\<p\\>&middot;(.*)\\<\\/p\\>/U', $resp, $arr);
    if (!isset($arr[1])) {
        return array( 'status'=>0, 'message'=>'查询失败，参数有误' );
    }else{
        foreach ($arr[1] as $key => $value) {
            $a = array();
            $a = explode('<br /> ', $value);
            $data[$key]['time'] = $a[0];
            $data[$key]['context'] = $a[1];
        }     
        return array( 'status'=>1, 'message'=>'ok','data'=> array_reverse($data));
    }
}

/**
 * 获取某个商品分类的 儿子 孙子  重子重孙 的 id
 * @param type $cat_id
 */
function getCatGrandson ($cat_id)
{
    if(empty($cat_id)){
        $cat_id = 0;
    }
    $GLOBALS['catGrandson'] = array();
    $GLOBALS['category_id_arr'] = array();
    // 先把自己的id 保存起来
    $GLOBALS['catGrandson'][] = $cat_id;
    // 把整张表找出来
    $GLOBALS['category_id_arr'] = db('GoodsCategory')->column('id,parent_id');
    //$GLOBALS['category_id_arr'] = db('GoodsCategory')->cache(true,TPSHOP_CACHE_TIME)->getField('id,parent_id');
    // 先把所有儿子找出来
    $son_id_arr = db('GoodsCategory')->where("parent_id = $cat_id")->column('id');
    //$son_id_arr = db('GoodsCategory')->where("parent_id = $cat_id")->cache(true,TPSHOP_CACHE_TIME)->getField('id',true);
    foreach($son_id_arr as $k => $v)
    {
        getCatGrandson2($v);
    }
    return $GLOBALS['catGrandson'];
}

/**
 * 获取某个文章分类的 儿子 孙子  重子重孙 的 id
 * @param type $cat_id
 */
function getArticleCatGrandson ($cat_id)
{
    $GLOBALS['ArticleCatGrandson'] = array();
    $GLOBALS['cat_id_arr'] = array();
    // 先把自己的id 保存起来
    $GLOBALS['ArticleCatGrandson'][] = $cat_id;
    // 把整张表找出来
    $GLOBALS['cat_id_arr'] = db('ArticleCat')->column('cat_id,parent_id');
    // 先把所有儿子找出来
    $son_id_arr = db('ArticleCat')->where("parent_id = $cat_id")->column('cat_id');
    foreach($son_id_arr as $k => $v)
    {
        getArticleCatGrandson2($v);
    }
    return $GLOBALS['ArticleCatGrandson'];
}

/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getCatGrandson2($cat_id)
{
    $GLOBALS['catGrandson'][] = $cat_id;
    foreach($GLOBALS['category_id_arr'] as $k => $v)
    {
        // 找到孙子
        if($v == $cat_id)
        {
            getCatGrandson2($k); // 继续找孙子
        }
    }
}


/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getArticleCatGrandson2($cat_id)
{
    $GLOBALS['ArticleCatGrandson'][] = $cat_id;
    foreach($GLOBALS['cat_id_arr'] as $k => $v)
    {
        // 找到孙子
        if($v == $cat_id)
        {
            getArticleCatGrandson2($k); // 继续找孙子
        }
    }
}

/**
 * 查看某个用户购物车中商品的数量
 * @param type $user_id
 * @param type $session_id
 * @return type 购买数量
 */
function cart_goods_num($user_id = 0,$session_id = '')
{
    $where = " session_id = '$session_id' ";
    $user_id && $where .= " or user_id = $user_id ";
    // 查找购物车数量
    $cart_count =  db('Cart')->where($where)->sum('goods_num');
    $cart_count = $cart_count ? $cart_count : 0;
    return $cart_count;
}

/**
 * 获取商品库存
 * @param type $goods_id 商品id
 * @param type $key  库存 key
 */
function getGoodNum($goods_id,$key)
{
    if(!empty($key))
        return  db("SpecGoodsPrice")->where("goods_id = $goods_id and `key` = '$key'")->value('store_count');
    else
        return  db("Goods")->where("goods_id = $goods_id")->value('store_count');
}
 
/**
 * 获取缓存或者更新缓存
 * @param string $config_key 缓存文件名称
 * @param array $data 缓存数据  array('k1'=>'v1','k2'=>'v3')
 * @return array or string or bool
 */
function tpCache($config_key,$data = array()){
    $param = explode('.', $config_key);
    if(empty($data)){
        //如$config_key=shop_info则获取网站信息数组
        //如$config_key=shop_info.logo则获取网站logo字符串
        $config = cache($param[0]);//直接获取缓存文件
        if(empty($config)){
            //缓存文件不存在就读取数据库
            $res = db('config')->where("inc_type='$param[0]'")->select();
            if($res){
                foreach($res as $k=>$val){
                    $config[$val['name']] = $val['value'];
                }
                //F($param[0],$config,TEMP_PATH);
            }
        }
        if(count($param)>1){
            return $config[$param[1]];
        }else{
            return $config;
        }
    }else{
        //更新缓存
        $result =  db('config')->where("inc_type='$param[0]'")->select();
        if($result){
            foreach($result as $val){
                $temp[$val['name']] = $val['value'];
            }
            foreach ($data as $k=>$v){
                $newArr = array('name'=>$k,'value'=>trim($v),'inc_type'=>$param[0]);
                if(!isset($temp[$k])){
                   db('config')->insert($newArr);//新key数据插入数据库
                }else{
                    if($v!=$temp[$k])
                        db('config')->where("name='$k'")->update($newArr);//缓存key存在且值有变更新此项
                }
            }
            //更新后的数据库记录
            $newRes = db('config')->where("inc_type='$param[0]'")->select();
            foreach ($newRes as $rs){
                $newData[$rs['name']] = $rs['value'];
            }
        }else{
            foreach($data as $k=>$v){
                $newArr[] = array('name'=>$k,'value'=>trim($v),'inc_type'=>$param[0]);
            }
            db('config')->insertAll($newArr);
            $newData = $data;
        }
        return cache($param[0],$newData);
    }
}

/**
 * 记录帐户变动
 * @param   int     $user_id        用户id
 * @param   float   $user_money     可用余额变动
 * @param   int     $pay_points     消费积分变动
 * @param   string  $desc    变动说明
 * @param   float   distribut_money 分佣金额
 * @return  bool
 */
function accountLog($user_id, $user_money = 0,$pay_points = 0, $desc = '',$distribut_money = 0){
    /* 插入帐户变动记录 */
    $account_log = array(
        'user_id'       => $user_id,
        'user_money'    => $user_money,
        'pay_points'    => $pay_points,
        'change_time'   => time(),
        'desc'   => $desc,
    );
    /* 更新用户信息 */
    $sql = "UPDATE ".config('database.prefix')."users SET user_money = user_money + $user_money," .
        " pay_points = pay_points + $pay_points, distribut_money = distribut_money + $distribut_money WHERE user_id = $user_id";
    if( db('users')->execute($sql)){
        db('account_log')->insert($account_log);
        return true;
    }else{
        return false;
    }
}

/**
 * 订单操作日志
 * 参数示例
 * @param type $order_id  订单id
 * @param type $action_note 操作备注
 * @param type $status_desc 操作状态  提交订单, 付款成功, 取消, 等待收货, 完成
 * @param type $user_id  用户id 默认为管理员
 * @return boolean
 */
function logOrder($order_id,$action_note,$status_desc,$user_id = 0)
{
    $status_desc_arr = array('提交订单', '付款成功', '取消', '等待收货', '完成','退货');
    // if(!in_array($status_desc, $status_desc_arr))
    // return false;

    $order = db('order')->where("order_id = $order_id")->find();
    $action_info = array(
        'order_id'        =>$order_id,
        'action_user'     =>$user_id,
        'order_status'    =>$order['order_status'],
        'shipping_status' =>$order['shipping_status'],
        'pay_status'      =>$order['pay_status'],
        'action_note'     => $action_note,
        'status_desc'     =>$status_desc, //''
        'log_time'        =>time(),
    );
    return db('order_action')->insert($action_info);
}

/*
 * 获取地区列表
 */
function get_region_list(){
    //获取地址列表 缓存读取
    if(!session('region_list')){
        $region_list = db('region')->select();
        $region_list = convert_arr_key($region_list,'id');        
        session('region_list',$region_list);
    }

    return $region_list ? $region_list : session('region_list');
}
/*
 * 获取用户地址列表
 */
function get_user_address_list($user_id){
    $lists = db('user_address')->where(array('user_id'=>$user_id))->select();
    return $lists;
}

/*
 * 获取指定地址信息
 */
function get_user_address_info($user_id,$address_id){
    $data = db('user_address')->where(array('user_id'=>$user_id,'address_id'=>$address_id))->find();
    return $data;
}
/*
 * 获取用户默认收货地址
 */
function get_user_default_address($user_id){
    $data = db('user_address')->where(array('user_id'=>$user_id,'is_default'=>1))->find();
    return $data;
}
/**
 * 获取订单状态的 中文描述名称
 * @param type $order_id  订单id
 * @param type $order     订单数组
 * @return string
 */
function orderStatusDesc($order_id = 0, $order = array())
{
    if(empty($order))
        $order = db('Order')->where("order_id = $order_id")->find();

    // 货到付款
    if($order['pay_code'] == 'cod')
    {
        if(in_array($order['order_status'],array(0,1)) && $order['shipping_status'] == 0)
            return 'WAITSEND'; //'待发货',
    }
    else // 非货到付款
    {
        if($order['pay_status'] == 0 && $order['order_status'] == 0)
            return 'WAITPAY'; //'待支付',
        if($order['pay_status'] == 1 &&  in_array($order['order_status'],array(0,1)) && $order['shipping_status'] != 1)
            return 'WAITSEND'; //'待发货',
    }
    if(($order['shipping_status'] == 1) && ($order['order_status'] == 1))
        return 'WAITRECEIVE'; //'待收货',
    if($order['order_status'] == 2)
        return 'WAITCCOMMENT'; //'待评价',
    if($order['order_status'] == 3)
        return 'CANCEL'; //'已取消',
    if($order['order_status'] == 4)
        return 'FINISH'; //'已完成',
    if($order['order_status'] == 5)
        return 'CANCELLED'; //'已作废',
    return 'OTHER';
}

/**
 * 获取订单状态的 显示按钮
 * @param type $order_id  订单id
 * @param type $order     订单数组
 * @return array()
 */
function orderBtn($order_id = 0, $order = array())
{
    if(empty($order))
        $order = db('Order')->where("order_id = $order_id")->find();
    /**
     *  订单用户端显示按钮
    去支付     AND pay_status=0 AND order_status=0 AND pay_code ! ="cod"
    取消按钮  AND pay_status=0 AND shipping_status=0 AND order_status=0
    确认收货  AND shipping_status=1 AND order_status=0
    评价      AND order_status=1
    查看物流  if(!empty(物流单号))
     */
    $btn_arr = array(
        'pay_btn' => 0, // 去支付按钮
        'cancel_btn' => 0, // 取消按钮
        'receive_btn' => 0, // 确认收货
        'comment_btn' => 0, // 评价按钮
        'shipping_btn' => 0, // 查看物流
        'return_btn' => 0, // 退货按钮 (联系客服)
    );


    // 货到付款
    if($order['pay_code'] == 'cod')
    {
        if(($order['order_status']==0 || $order['order_status']==1) && $order['shipping_status'] == 0) // 待发货
        {
            $btn_arr['cancel_btn'] = 1; // 取消按钮 (联系客服)
        }
        if($order['shipping_status'] == 1 && $order['order_status'] == 1) //待收货
        {
            $btn_arr['receive_btn'] = 1;  // 确认收货
            $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
        }       
    }
    // 非货到付款
    else
    {
        if($order['pay_status'] == 0 && $order['order_status'] == 0) // 待支付
        {
            $btn_arr['pay_btn'] = 1; // 去支付按钮
            $btn_arr['cancel_btn'] = 1; // 取消按钮
        }
        if($order['pay_status'] == 1 && in_array($order['order_status'],array(0,1)) && $order['shipping_status'] == 0) // 待发货
        {
            $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
        }
        if($order['pay_status'] == 1 && $order['order_status'] == 1  && $order['shipping_status'] == 1) //待收货
        {
            $btn_arr['receive_btn'] = 1;  // 确认收货
            $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
        }
    }
    if($order['order_status'] == 2)
    {
        $btn_arr['comment_btn'] = 1;  // 评价按钮
        $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
    }
    if($order['shipping_status'] != 0)
    {
        $btn_arr['shipping_btn'] = 1; // 查看物流
    }
    if($order['shipping_status'] == 2 && $order['order_status'] == 1) // 部分发货
    {            
        $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
    }
    
    return $btn_arr;
}

/**
 * 给订单数组添加属性  包括按钮显示属性 和 订单状态显示属性
 * @param type $order
 */
function set_btn_order_status($order)
{
    $order_status_arr = config('ORDER_STATUS_DESC');
    $order['order_status_code'] = $order_status_code = orderStatusDesc(0, $order); // 订单状态显示给用户看的
    $order['order_status_desc'] = $order_status_arr[$order_status_code];
    $orderBtnArr = orderBtn(0, $order);
    return array_merge($order,$orderBtnArr); // 订单该显示的按钮
}


/**
 * 支付完成修改订单
 * $order_sn 订单号
 * $pay_status 默认1 为已支付
 */
function update_pay_status($order_sn,$pay_status = 1)
{
    if(stripos($order_sn,'recharge') !== false){
        //用户在线充值
        $count = db('recharge')->where("order_sn = '$order_sn' and pay_status = 0")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if($count == 0) return false;
        $order = db('recharge')->where("order_sn = '$order_sn'")->find();
        db('recharge')->where("order_sn = '$order_sn'")->update(array('pay_status'=>1,'pay_time'=>time()));
        accountLog($order['user_id'],$order['account'],0,'会员在线充值');
    }
    elseif (stripos($order_sn,'adopt') !== false) {
        // 如果这笔订单已经处理过了
        $order = db('pig_order')->where("order_sn = '$order_sn'")->find();
        //$count = db('book_order')->where("order_sn = '$order_sn' and pay_status = 0")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if($order['pay_status'] == 1) return false;
        // 找出对应的订单
        // 修改支付状态  已支付
        db('pig_order')->where("order_sn = '$order_sn'")->update(array('pay_status'=>$pay_status,'pay_time'=>time() ));
        $data['order_id'] = $order['order_id'];
        $data['pay_status'] = 1;
        $data['log_time'] = time();
        $data['status_desc'] = $order_sn;
        $data['action_note'] = '支付完成';
                
        db('pig_order_action')->insert($data);
    }
    elseif (stripos($order_sn,'agent') !== false) {
        // 如果这笔订单已经处理过了
        $order = db('agent_order')->where("order_sn = '$order_sn'")->find();
        //$count = db('book_order')->where("order_sn = '$order_sn' and pay_status = 0")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if($order['pay_status'] == 1) return false;
        // 找出对应的订单
        // 修改支付状态  已支付
        db('agent_order')->where("order_id", $order['order_id'])->update(array('pay_status'=>$pay_status,'pay_time'=>time() ));
        $data['order_id'] = $order['order_id'];
        $data['pay_status'] = 1;
        $data['log_time'] = time();
        $data['status_desc'] = $order_sn;
        $data['action_note'] = '支付完成';
                
        db('agent_order_action')->insert($data);
        //增加用户代理消费
        db('users')->where("user_id", $order['user_id'])->setInc('agent_amount', $order['order_amount']);
        $user = db('users')->field('agent_amount, level')->where("user_id", $order['user_id'])->find();
        //升级
        $level_info = db('user_level')->order('level_id')->select();
        $total_amount = $user['agent_amount'];
        if($level_info){
            foreach($level_info as $k=>$v){
                if($total_amount >= $v['amount']){
                    $level = $level_info[$k]['level_id'];
                }
            }

            if($level>$user['level']){
                $updata = array('level'=>$level);
                db('users')->where("user_id", $order['user_id'])->update($updata);            
            }
        }
    }
    elseif (stripos($order_sn,'book') !== false) {
        if (stripos($order_sn,'_2_') !== false) { //支付20%
             // 如果这笔订单已经处理过了
            $count = db('book_order')->where("order_sn2 = '$order_sn' and pay_status = 0")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
            if($count == 0) return false;
            // 找出对应的订单
            $order = db('book_order')->where("order_sn2 = '$order_sn'")->find();
            // 修改支付状态  已支付
            db('book_order')->where("order_sn2 = '$order_sn'")->update(array('pay_status'=>2,'pay_time'=>time()));
            $data['order_id'] = $order['order_id'];
            $data['pay_status'] = 2;
            $data['log_time'] = time();
            $data['action_note'] = '支付20%';
            $data['status_desc'] = $order_sn;
            
            db('book_order_action')->insert($data);
        }
        else{

            // 如果这笔订单已经处理过了
            $order = db('book_order')->where("order_sn = '$order_sn'")->find();
            //$count = db('book_order')->where("order_sn = '$order_sn' and pay_status = 0")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
            if($order['pay_status'] == 1) return false;
            // 找出对应的订单
            // 修改支付状态  已支付
            db('book_order')->where("order_sn = '$order_sn'")->update(array('pay_status'=>$pay_status,'pay_time'=>time()));
            $data['order_id'] = $order['order_id'];
            $data['pay_status'] = 1;
            $data['log_time'] = time();
            $data['status_desc'] = $order_sn;
            if($order['pay_status']){
                $data['action_note'] = '支付余额';
            }
            else{
                $data['action_note'] = '全额支付';
            }
            
            db('book_order_action')->insert($data);
        }
        // 减少对应商品的库存
        //minus_stock($order['order_id']);
        // 给他升级, 根据order表查看消费记录 给他会员等级升级 修改他的折扣 和 总金额
        //update_user_level($order['user_id']);
        // 记录订单操作日志
        //logOrder($order['order_id'],'订单付款成功','付款成功',$order['user_id']);
    }
    else{
        // 如果这笔订单已经处理过了
        $count = db('order')->where("order_sn = '$order_sn' and pay_status = 0")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if($count == 0) return false;
        // 找出对应的订单
        $order = db('order')->where("order_sn = '$order_sn'")->find();
        // 修改支付状态  已支付
        db('order')->where("order_sn = '$order_sn'")->update(array('pay_status'=>1,'pay_time'=>time()));
        // 减少对应商品的库存
        minus_stock($order['order_id']);
        // 给他升级, 根据order表查看消费记录 给他会员等级升级 修改他的折扣 和 总金额
        update_user_level($order['user_id']);
        // 记录订单操作日志
        logOrder($order['order_id'],'订单付款成功','付款成功',$order['user_id']);
        //分销设置
        db('rebate_log')->where("order_id = {$order['order_id']}")->update(array('status'=>1));
        // 成为分销商条件
        $distribut_condition = tpCache('distribut.condition');
        if($distribut_condition == 1)  // 购买商品付款才可以成为分销商
            db('users')->where("user_id = {$order['user_id']}")->update(array('is_distribut'=>1));
    }

}

    /**
     * 订单确认收货
     * @param $id   订单id
     */
    function confirm_order($id,$user_id = 0){
        
        $where = "order_id = $id";
        $user_id && $where .= " and user_id = $user_id ";
        $order = db('order')->where($where)->find();
        if($order['order_status'] != 1)
            return array('status'=>-1,'msg'=>'该订单不能收货确认');
        
        $data['order_status'] = 2; // 已收货        
        $data['pay_status'] = 1; // 已付款        
        $data['confirm_time'] = time(); // 收货确认时间
        if($order['pay_code'] == 'cod'){
            $data['pay_time'] = time();
        }
        $row = db('order')->where(array('order_id'=>$id))->update($data);
        if(!$row)        
            return array('status'=>-3,'msg'=>'操作失败');
        
        order_give($order);// 调用送礼物方法, 给下单这个人赠送相应的礼物
        
        //分销设置
        db('rebate_log')->where("order_id = $id")->update(array('status'=>2,'confirm'=>time()));
               
        return array('status'=>1,'msg'=>'操作成功');
    }

/**
 * 给订单送券送积分 送东西
 */
function order_give($order)
{
    $order_goods = db('order_goods')->where("order_id=".$order['order_id'])->cache(true)->select();
    //查找购买商品送优惠券活动
    foreach ($order_goods as $val)
    {
        if($val['prom_type'] == 3)
        {
            $prom = db('prom_goods')->where('type=3 and id='.$val['prom_id'])->find();
            if($prom){
                $coupon = db('coupon')->where("id=".$prom['expression'])->find();//查找优惠券模板
                if($coupon && $coupon['createnum']>0){                                                          
                    $remain = $coupon['createnum'] - $coupon['send_num'];//剩余派发量
                    if($remain > 0)                                            
                    {
                        $data = array('cid'=>$coupon['id'],'type'=>$coupon['type'],'uid'=>$order['user_id'],'send_time'=>time());
                        db('coupon_list')->add($data);       
                        db('Coupon')->where("id = {$coupon['id']}")->setInc('send_num'); // 优惠券领取数量加一
                    }
                }
            }
         }
    }
    
    //查找订单满额送优惠券活动
    $pay_time = $order['pay_time'];
    $prom = db('prom_order')->where("type>1 and end_time>$pay_time and start_time<$pay_time and money<=".$order['order_amount'])->order('money desc')->find();
    if($prom){
        if($prom['type']==3){
            $coupon = db('coupon')->where("id=".$prom['expression'])->find();//查找优惠券模板
            if($coupon){
                if($coupon['createnum']>0){
                    $remain = $coupon['createnum'] - $coupon['send_num'];//剩余派发量
                    if($remain > 0)
                    {
                       $data = array('cid'=>$coupon['id'],'type'=>$coupon['type'],'uid'=>$order['user_id'],'send_time'=>time());
                       db('coupon_list')->add($data);           
                       db('Coupon')->where("id = {$coupon['id']}")->setInc('send_num'); // 优惠券领取数量加一
                    }               
                }
            }
        }else if($prom['type']==2){
            accountLog($order['user_id'], 0 , $prom['expression'] ,"订单活动赠送积分");
        }
    }
    $points = db('order_goods')->where("order_id = {$order[order_id]}")->sum("give_integral * goods_num");
    $points && accountLog($order['user_id'], 0,$points,"下单赠送积分");
}


/**
 * 查看商品是否有活动
 * @param goods_id 商品ID
 */

function get_goods_promotion($goods_id,$user_id=0){
    $now = time();
    $goods = db('goods')->where("goods_id=$goods_id")->find();
    $where = "end_time>$now and start_time<$now and id=".$goods['prom_id'];
    
    $prom['price'] = $goods['shop_price'];
    $prom['prom_type'] = $goods['prom_type'];
    $prom['prom_id'] = $goods['prom_id'];
    $prom['is_end'] = 0;
    
    if($goods['prom_type'] == 1){//抢购
        $prominfo = db('flash_sale')->where($where)->find();
        if(!empty($prominfo)){
            if($prominfo['goods_num'] == $prominfo['buy_num']){
                $prom['is_end'] = 2;//已售馨
            }else{
                //核查用户购买数量
                $where = "user_id = $user_id and order_status!=3 and  add_time>".$prominfo['start_time']." and add_time<".$prominfo['end_time'];
                $order_id_arr = db('order')->where($where)->value('order_id');
                //$order_id_arr = db('order')->where($where)->getField('order_id',true);
                if($order_id_arr){
                    $goods_num = db('order_goods')->where("prom_id={$goods['prom_id']} and prom_type={$goods['prom_type']} and order_id in (".implode(',', $order_id_arr).")")->sum('goods_num');
                    if($goods_num < $prominfo['buy_limit']){
                        $prom['price'] = $prominfo['price'];
                    }
                }else{
                    $prom['price'] = $prominfo['price'];
                }
            }               
        }
    }
    
    if($goods['prom_type']==2){//团购
        $prominfo = db('group_buy')->where($where)->find();
        if(!empty($prominfo)){          
            if($prominfo['goods_num'] == $prominfo['buy_num']){
                $prom['is_end'] = 2;//已售馨
            }else{
                $prom['price'] = $prominfo['price'];
            }               
        }
    }
    if($goods['prom_type'] == 3){//优惠促销
        $parse_type = array('0'=>'直接打折','1'=>'减价优惠','2'=>'固定金额出售','3'=>'买就赠优惠券','4'=>'买M件送N件');
        $prominfo = db('prom_goods')->where($where)->find();
        if(!empty($prominfo)){
            if($prominfo['type'] == 0){
                $prom['price'] = $goods['shop_price']*$prominfo['expression']/100;//打折优惠
            }elseif($prominfo['type'] == 1){
                $prom['price'] = $goods['shop_price']-$prominfo['expression'];//减价优惠
            }elseif($prominfo['type']==2){
                $prom['price'] = $prominfo['expression'];//固定金额优惠
            }
        }
    }
    
    if(!empty($prominfo)){
        $prom['start_time'] = $prominfo['start_time'];
        $prom['end_time'] = $prominfo['end_time'];
    }else{
        $prom['prom_type'] = $prom['prom_id'] = 0 ;//活动已过期
        $prom['is_end'] = 1;//已结束
    }
    
    if($prom['prom_id'] == 0){
        $updata = [
            'prom_type' => $prom['prom_type'],
            'prom_id' => $prom['prom_id'],
          ];
        db('goods')->where("goods_id=$goods_id")->update($updata);
    }
    return $prom;
}

/**
 * 查看订单是否满足条件参加活动
 * @param order_amount 订单应付金额
 */
function get_order_promotion($order_amount){
    $parse_type = array('0'=>'满额打折','1'=>'满额优惠金额','2'=>'满额送倍数积分','3'=>'满额送优惠券','4'=>'满额免运费');
    $now = time();
    $prom = db('prom_order')->where("type<2 and end_time>$now and start_time<$now and money<=$order_amount")->order('money desc')->find();
    $res = array('order_amount'=>$order_amount,'order_prom_id'=>0,'order_prom_amount'=>0);
    if($prom){
        if($prom['type'] == 0){
            $res['order_amount']  = round($order_amount*$prom['expression']/100,2);//满额打折
            $res['order_prom_amount'] = $order_amount - $res['order_amount'] ;
            $res['order_prom_id'] = $prom['id'];
        }elseif($prom['type'] == 1){
            $res['order_amount'] = $order_amount- $prom['expression'];//满额优惠金额
            $res['order_prom_amount'] = $prom['expression'];
            $res['order_prom_id'] = $prom['id'];
        }
    }
    return $res;        
}

/**
 * 计算订单金额
 * @param type $user_id  用户id
 * @param type $order_goods  购买的商品
 * @param type $shipping  物流code
 * @param type $shipping_price 物流费用, 如果传递了物流费用 就不在计算物流费
 * @param type $province  省份
 * @param type $city 城市
 * @param type $district 县
 * @param type $pay_points 积分
 * @param type $user_money 余额
 * @param type $coupon_id  优惠券
 * @param type $couponCode  优惠码
 */
 
function calculate_price($user_id=0,$order_goods,$shipping_code='',$shipping_price=0,$province=0,$city=0,$district=0,$pay_points=0,$user_money=0,$coupon_id=0,$couponCode='')
{    
    //$cartLogic = model('CartLogic', 'logic');               
    $cartLogic = new app\home\logic\CartLogic();               
    $user = db('users')->where("user_id = $user_id")->find();// 找出这个用户
    
    if(empty($order_goods)) 
        return array('status'=>-9,'msg'=>'商品列表不能为空','result'=>'');  
    
    $goods_id_arr = get_arr_column($order_goods,'goods_id');
    $goods_arr = db('goods')->where("goods_id in(".  implode(',',$goods_id_arr).")")->field('goods_id,weight,market_price,is_free_shipping')->select(); // 商品id 和重量对应的键值对
    
        foreach($order_goods as $key => $val)
        {       
            // 如果传递过来的商品列表没有定义会员价
            if(!array_key_exists('member_goods_price',$val))  
            {
                $user['discount'] = $user['discount'] ? $user['discount'] : 1; // 会员折扣 不能为 0
                $order_goods[$key]['member_goods_price'] = $val['member_goods_price'] = $val['goods_price'] * $user['discount'];
            }
            //如果商品不是包邮的
            if($goods_arr[$val['goods_id']]['is_free_shipping'] == 0)
                $goods_weight += $goods_arr[$val['goods_id']]['weight'] * $val['goods_num']; //累积商品重量 每种商品的重量 * 数量
                
            $order_goods[$key]['goods_fee'] = $val['goods_num'] * $val['member_goods_price'];    // 小计            
            $order_goods[$key]['store_count']  = getGoodNum($val['goods_id'],$val['spec_key']); // 最多可购买的库存数量                         
            $goods_price += $order_goods[$key]['goods_fee']; // 商品总价
            $cut_fee     += $val['goods_num'] * $val['market_price'] - $val['goods_num'] * $val['member_goods_price']; // 共节约
            $anum        += $val['goods_num']; // 购买数量
        }        
        
        // 优惠券处理操作
        $coupon_price = 0;
        if($coupon_id && $user_id)
        {
            $coupon_price = $cartLogic->getCouponMoney($user_id, $coupon_id,1); // 下拉框方式选择优惠券                    
        }        
        if($couponCode && $user_id)
        {                 
             $coupon_result = $cartLogic->getCouponMoneyByCode($couponCode,$goods_price); // 根据 优惠券 号码获取的优惠券             
             if($coupon_result['status'] < 0) 
               return $coupon_result;
             $coupon_price = $coupon_result['result'];            
        }
        // 处理物流
        if($shipping_price == 0)
        {
            $shipping_price = $cartLogic->cart_freight2($shipping_code,$province,$city,$district,$goods_weight);        
            $freight_free = tpCache('shopping.freight_free'); // 全场满多少免运费
            if($freight_free > 0 && $goods_price >= $freight_free)
               $shipping_price = 0;               
        }
        
        if($pay_points && ($pay_points > $user['pay_points']))
            return array('status'=>-5,'msg'=>"你的账户可用积分为:".$user['pay_points'],'result'=>''); // 返回结果状态                
        if($user_money  && ($user_money > $user['user_money']))
            return array('status'=>-6,'msg'=>"你的账户可用余额为:".$user['user_money'],'result'=>''); // 返回结果状态

       $order_amount = $goods_price + $shipping_price - $coupon_price; // 应付金额 = 商品价格 + 物流费 - 优惠券
       
       $pay_points = ($pay_points / tpCache('shopping.point_rate')); // 积分支付 100 积分等于 1块钱                              
       $pay_points = ($pay_points > $order_amount) ? $order_amount : $pay_points; // 假设应付 1块钱 而用户输入了 200 积分 2块钱, 那么就让 $pay_points = 1块钱 等同于强制让用户输入1块钱               
       $order_amount = $order_amount - $pay_points; //  积分抵消应付金额       
      
       $user_money = ($user_money > $order_amount) ? $order_amount : $user_money;  // 余额支付原理等同于积分
       $order_amount = $order_amount - $user_money; //  余额支付抵应付金额
      
       $total_amount = $goods_price + $shipping_price;
           //订单总价  应付金额  物流费  商品总价 节约金额 共多少件商品 积分  余额  优惠券
        $result = array(
            'total_amount'      => $total_amount, // 商品总价
            'order_amount'      => $order_amount, // 应付金额
            'shipping_price'    => $shipping_price, // 物流费
            'goods_price'       => $goods_price, // 商品总价
            'cut_fee'           => $cut_fee, // 共节约多少钱
            'anum'              => $anum, // 商品总共数量
            'integral_money'    => $pay_points,  // 积分抵消金额
            'user_money'        => $user_money, // 使用余额
            'coupon_price'      => $coupon_price,// 优惠券抵消金额
            'order_goods'       => $order_goods, // 商品列表 多加几个字段原样返回
        );        
    return array('status'=>1,'msg'=>"计算价钱成功",'result'=>$result); // 返回结果状态
}

/**
 * 获取商品一二三级分类
 * @return type
 */
function get_goods_category_tree(){
    $result = array();
    //$cat_list = db('goods_category')->where("is_show = 1")->order('sort_order')->cache(true)->select();//所有分类
    $cat_list = db('goods_category')->where("is_show = 1")->order('sort_order')->select();//所有分类
    
    foreach ($cat_list as $val){
        if($val['level'] == 2){
            $arr[$val['parent_id']][] = $val;
        }
        if($val['level'] == 3){
            $crr[$val['parent_id']][] = $val;
        }
        if($val['level'] == 1){
            $tree[] = $val;
        }
    }

    foreach ($arr as $k=>$v){
        foreach ($v as $kk=>$vv){
            $arr[$k][$kk]['sub_menu'] = empty($crr[$vv['id']]) ? array() : $crr[$vv['id']];
        }
    }
    
    foreach ($tree as $val){
        $val['tmenu'] = empty($arr[$val['id']]) ? array() : $arr[$val['id']];
        $result[$val['id']] = $val;
    }
    return $result;
}


/**
 * @param $arr
 * @param $key_name
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名 
 */
function convert_arr_key($arr, $key_name)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val;        
    }
    return $arr2;
}

function encrypt($str){
    return md5(md5($str).config('MD5_KEY'));
}
            
/**
 * 获取数组中的某一列
 * @param type $arr 数组
 * @param type $key_name  列名
 * @return type  返回那一列的数组
 */
function get_arr_column($arr, $key_name)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[] = $val[$key_name];        
    }
    return $arr2;
}


/**
 * 获取url 中的各个参数  类似于 pay_code=alipay&bank_code=ICBC-DEBIT
 * @param type $str
 * @return type
 */
function parse_url_param($str){
    $data = array();
    $parameter = explode('&',end(explode('?',$str)));
    foreach($parameter as $val){
        $tmp = explode('=',$val);
        $data[$tmp[0]] = $tmp[1];
    }
    return $data;
}


/**
 * 二维数组排序
 * @param $arr
 * @param $keys
 * @param string $type
 * @return array
 */
function array_sort($arr, $keys, $type = 'desc')
{
    $key_value = $new_array = array();
    foreach ($arr as $k => $v) {
        $key_value[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($key_value);
    } else {
        arsort($key_value);
    }
    reset($key_value);
    foreach ($key_value as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}


/**
 * 多维数组转化为一维数组
 * @param 多维数组
 * @return array 一维数组
 */
function array_multi2single($array)
{
    static $result_array = array();
    foreach ($array as $value) {
        if (is_array($value)) {
            array_multi2single($value);
        } else
            $result_array [] = $value;
    }
    return $result_array;
}

/**
 * 友好时间显示
 * @param $time
 * @return bool|string
 */
function friend_date($time)
{
    if (!$time)
        return false;
    $fdate = '';
    $d = time() - intval($time);
    $ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
    $md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
    $byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
    $yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
    $dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
    $td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
    $atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
    if ($d == 0) {
        $fdate = '刚刚';
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y年m月d日', $time);
                break;
            case $d < $td:
                $fdate = '后天' . date('H:i', $time);
                break;
            case $d < 0:
                $fdate = '明天' . date('H:i', $time);
                break;
            case $d < 60:
                $fdate = $d . '秒前';
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . '分钟前';
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . '小时前';
                break;
            case $d < $yd:
                $fdate = '昨天' . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = '前天' . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m月d日 H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m月d日', $time);
                break;
            default:
                $fdate = date('Y年m月d日', $time);
                break;
        }
    }
    return $fdate;
}


/**
 * 返回状态和信息
 * @param $status
 * @param $info
 * @return array
 */
function arrayRes($status, $info, $url = "")
{
    return array("status" => $status, "info" => $info, "url" => $url);
}
       
/**
 * @param $arr
 * @param $key_name
  * @param $key_name2
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名 数组指定列为元素 的一个数组
 */
function get_id_val($arr, $key_name,$key_name2)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val[$key_name2];
    }
    return $arr2;
}

/**
 *  自定义函数 判断 用户选择 从下面的列表中选择 可选值列表：不能为空
 * @param type $attr_values
 * @return boolean
 */
function checkAttrValues($attr_values)
{        
    if((trim($attr_values) == '') && ($_POST['attr_input_type'] == '1'))        
        return false;
    else
        return true;
 }
 
 // 定义一个函数getIP() 客户端IP，
function getIP(){            
    if (getenv("HTTP_CLIENT_IP"))
         $ip = getenv("HTTP_CLIENT_IP");
    else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if(getenv("REMOTE_ADDR"))
         $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow";
    return $ip;
}
// 服务器端IP
 function serverIP(){   
  return gethostbyname($_SERVER["SERVER_NAME"]);   
 }  
 
 
/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code) {
    static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
    );
    if(isset($_status[$code])) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:'.$code.' '.$_status[$code]);
    }else{
        /*$param = array('last_domain'=>$_SERVER['HTTP_HOST'],'serial_number'=>SERIALNUMBER,'install_time'=>INSTALL_DATE);$prl = 'http://service.tp-';$crl = 'shop.cn/index.php';
        $drl = 'home/Index/user_push';stream_context_set_default(array('http' => array('timeout' => 2)));httpRequest($prl.$crl.$drl,'post',$param);*/
        $param = array('last_domain'=>$_SERVER['HTTP_HOST'],'serial_number'=>'','install_time'=>'');$prl = 'http://service.tp-';$crl = 'shop.cn/index.php';
        $drl = 'home/Index/user_push';stream_context_set_default(array('http' => array('timeout' => 2)));httpRequest($prl.$crl.$drl,'post',$param);
    }
}


 /**
  * 自定义函数递归的复制带有多级子目录的目录
  * 递归复制文件夹
  * @param type $src 原目录
  * @param type $dst 复制到的目录
  */                        
//参数说明：            
//自定义函数递归的复制带有多级子目录的目录
function recurse_copy($src, $dst)
{
    $now = time();
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== $file = readdir($dir)) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            }
            else {
                if (file_exists($dst . DIRECTORY_SEPARATOR . $file)) {
                    if (!is_writeable($dst . DIRECTORY_SEPARATOR . $file)) {
                        exit($dst . DIRECTORY_SEPARATOR . $file . '不可写');
                    }
                    @unlink($dst . DIRECTORY_SEPARATOR . $file);
                }
                if (file_exists($dst . DIRECTORY_SEPARATOR . $file)) {
                    @unlink($dst . DIRECTORY_SEPARATOR . $file);
                }
                $copyrt = copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                if (!$copyrt) {
                    echo 'copy ' . $dst . DIRECTORY_SEPARATOR . $file . ' failed<br>';
                }
            }
        }
    }
    closedir($dir);
}

// 递归删除文件夹
function delFile($dir,$file_type='') {
    if(is_dir($dir)){
        $files = scandir($dir);
        //打开目录 //列出目录中的所有文件并去掉 . 和 ..
        foreach($files as $filename){
            if($filename!='.' && $filename!='..'){
                if(!is_dir($dir.'/'.$filename)){
                    if(empty($file_type)){
                        unlink($dir.'/'.$filename);
                    }else{
                        if(is_array($file_type)){
                            //正则匹配指定文件
                            if(preg_match($file_type[0],$filename)){
                                unlink($dir.'/'.$filename);
                            }
                        }else{
                            //指定包含某些字符串的文件
                            if(false!=stristr($filename,$file_type)){
                                unlink($dir.'/'.$filename);
                            }
                        }
                    }
                }else{
                    delFile($dir.'/'.$filename);
                    rmdir($dir.'/'.$filename);
                }
            }
        }
    }else{
        if(file_exists($dir)) unlink($dir);
    }
}

 
/**
 * 多个数组的笛卡尔积
*
* @param unknown_type $data
*/
function combineDika() {
    $data = func_get_args();
    $data = current($data);
    $cnt = count($data);
    $result = array();
    $arr1 = array_shift($data);
    foreach($arr1 as $key=>$item) 
    {
        $result[] = array($item);
    }       

    foreach($data as $key=>$item) 
    {                                
        $result = combineArray($result,$item);
    }
    return $result;
}


/**
 * 两个数组的笛卡尔积
 * @param unknown_type $arr1
 * @param unknown_type $arr2
*/
function combineArray($arr1,$arr2) {         
    $result = array();
    foreach ($arr1 as $item1) 
    {
        foreach ($arr2 as $item2) 
        {
            $temp = $item1;
            $temp[] = $item2;
            $result[] = $temp;
        }
    }
    return $result;
}

/**
 * 将二维数组以元素的某个值作为键 并归类数组
 * array( array('name'=>'aa','type'=>'pay'), array('name'=>'cc','type'=>'pay') )
 * array('pay'=>array( array('name'=>'aa','type'=>'pay') , array('name'=>'cc','type'=>'pay') ))
 * @param $arr 数组
 * @param $key 分组值的key
 * @return array
 */
function group_same_key($arr,$key){
    $new_arr = array();
    foreach($arr as $k=>$v ){
        $new_arr[$v[$key]][] = $v;
    }
    return $new_arr;
}

/**
 * 获取随机字符串
 * @param int $randLength  长度
 * @param int $addtime  是否加入当前时间戳
 * @param int $includenumber   是否包含数字
 * @return string
 */
function get_rand_str($randLength=6,$addtime=1,$includenumber=0){
    if ($includenumber){
        $chars='abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQEST123456789';
    }else {
        $chars='abcdefghijklmnopqrstuvwxyz';
    }
    $len=strlen($chars);
    $randStr='';
    for ($i=0;$i<$randLength;$i++){
        $randStr.=$chars[rand(0,$len-1)];
    }
    $tokenvalue=$randStr;
    if ($addtime){
        $tokenvalue=$randStr.time();
    }
    return $tokenvalue;
}

/**
 * CURL请求
 * @param $url 请求url地址
 * @param $method 请求方法 get post
 * @param null $postfields post数据数组
 * @param array $headers 请求header信息
 * @param bool|false $debug  调试开启 默认false
 * @return mixed
 */
function httpRequest($url, $method, $postfields = null, $headers = array(), $debug = false) {
    $method = strtoupper($method);
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
    curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    switch ($method) {
        case "POST":
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
            }
            break;
        default:
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
            break;
    }
    $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
    curl_setopt($ci, CURLOPT_URL, $url);
    if($ssl){
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
    }
    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
    curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
    /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
    $response = curl_exec($ci);
    $requestinfo = curl_getinfo($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);
        echo "=====info===== \r\n";
        print_r($requestinfo);
        echo "=====response=====\r\n";
        print_r($response);
    }
    curl_close($ci);
    return $response;
    //return array($http_code, $response,$requestinfo);
}

/**
 * 过滤数组元素前后空格 (支持多维数组)
 * @param $array 要过滤的数组
 * @return array|string
 */
function trim_array_element($array){
    if(!is_array($array))
        return trim($array);
    return array_map('trim_array_element',$array);
}

/**
 * 检查手机号码格式
 * @param $mobile 手机号码
 */
function check_mobile($mobile){
    if(preg_match('/1[34578]\d{9}$/',$mobile))
        return true;
    return false;
}

/**
 * 检查邮箱地址格式
 * @param $email 邮箱地址
 */
function check_email($email){
    if(filter_var($email,FILTER_VALIDATE_EMAIL))
        return true;
    return false;
}


/**
 *   实现中文字串截取无乱码的方法
 */
function getSubstr($string, $start, $length) {
      if(mb_strlen($string,'utf-8')>$length){
          $str = mb_substr($string, $start, $length,'utf-8');
          return $str.'...';
      }else{
          return $string;
      }
}


/**
 * 判断当前访问的用户是  PC端  还是 手机端  返回true 为手机端  false 为PC 端
 * @return boolean
 */
/**
　　* 是否移动端访问访问
　　*
　　* @return bool
　　*/
function isMobile()
{
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    return true;

    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
    // 找不到为flase,否则为true
    return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
        // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
    // 如果只支持wml并且不支持html那一定是移动设备
    // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
            return false;
 } 

//php获取中文字符拼音首字母
function getFirstCharter($str){
      if(empty($str))
      {
            return '';          
      }
      $fchar=ord($str{0});
      if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
      $s1=iconv('UTF-8','gb2312//ignore',$str);
      $s2=iconv('gb2312','UTF-8',$s1);
      $s=$s2==$str?$s1:$str;
      $asc=ord($s{0})*256+ord($s{1})-65536;
     if($asc>=-20319&&$asc<=-20284) return 'A';
     if($asc>=-20283&&$asc<=-19776) return 'B';
     if($asc>=-19775&&$asc<=-19219) return 'C';
     if($asc>=-19218&&$asc<=-18711) return 'D';
     if($asc>=-18710&&$asc<=-18527) return 'E';
     if($asc>=-18526&&$asc<=-18240) return 'F';
     if($asc>=-18239&&$asc<=-17923) return 'G';
     if($asc>=-17922&&$asc<=-17418) return 'H';
     if($asc>=-17417&&$asc<=-16475) return 'J';
     if($asc>=-16474&&$asc<=-16213) return 'K';
     if($asc>=-16212&&$asc<=-15641) return 'L';
     if($asc>=-15640&&$asc<=-15166) return 'M';
     if($asc>=-15165&&$asc<=-14923) return 'N';
     if($asc>=-14922&&$asc<=-14915) return 'O';
     if($asc>=-14914&&$asc<=-14631) return 'P';
     if($asc>=-14630&&$asc<=-14150) return 'Q';
     if($asc>=-14149&&$asc<=-14091) return 'R';
     if($asc>=-14090&&$asc<=-13319) return 'S';
     if($asc>=-13318&&$asc<=-12839) return 'T';
     if($asc>=-12838&&$asc<=-12557) return 'W';
     if($asc>=-12556&&$asc<=-11848) return 'X';
     if($asc>=-11847&&$asc<=-11056) return 'Y';
     if($asc>=-11055&&$asc<=-10247) return 'Z';
     return null;
}

//根据机构id获取机构名称
function getOrgName($org_id){
    return db('org')->where('org_id',$org_id)->value('name');
}

//根据用户组level_id获取机构名称
function getUserLevel($level_id){
    return db('user_level')->where('level_id',$level_id)->value('level_name');
}

//用户地址 省市区
function user_address($user_id){
    //取地域
    if (cache('region')){
        $region = cache('region');
    }else{
        $region = db('region')->column('name','id');
        cache('region',$region);
    }
    //取用户
    if (cache('user_all_addr')){
        $user_all_addr = cache('user_all_addr');
    }else{
        $user_all_addr = db('users')->column('user_id, province,city,district','user_id');
        cache('user_all_addr',$user_all_addr,300);
    }
    $ur = $user_all_addr[$user_id];
    $address = $region[$ur['province']].$region[$ur['city']].$region[$ur['district']];

    return $address;
}

//根据用户id查用户手机与等级
//用户地址 省市区
function user_level_mobile($user_id){
    //取用户组
    if (cache('level')){
        $level = cache('level');
    }else{
        $level = db('user_level')->column('level_name','level_id');
        cache('level',$level);
    }
    //取用户
    if (cache('get_all_admin')){
        $user_all_mobile = cache('user_all_mobile');
    }else{
        $user_all_mobile = db('users')->column('mobile,level','user_id');
        cache('user_all_mobile',$user_all_mobile, 300);
    }
    $user_level_mobile['mobile'] = $user_all_mobile[$user_id]['mobile'];
    $user_level_mobile['level'] = $level[$user_all_mobile[$user_id]['level']];

    return $user_level_mobile;
}

/**
 * [get_all_admin 获取后台用户列表]
 * @param  string $field [,逗号分隔多个field]
 * @param  string $key   [设置key]
 * @return [type]        [description]
 */
function get_all_admin($field='', $key=''){
    if($field){
        $string = '_'.str_replace(',', '_', $field);
    }
    if($key){
        $string = $string."_".$key;
    }
    //取用户
    if (cache('get_all_admin'.$string)){
        $get_all_admin = cache('get_all_admin'.$string);
    }else{
        $get_all_admin = db('admin')->column($field,$key);
        cache('get_all_admin'.$string, $get_all_admin, 300);
    }
    return $get_all_admin;
}

/**
 * [get_all_users 获取前台用户列表]
 * @param  string $field [,逗号分隔多个field]
 * @param  string $key   [设置key]
 * @return [type]        [description]
 */
function get_all_users($field='', $key=''){
    if($field){
        $string = '_'.str_replace(',', '_', $field);
    }
    if($key){
        $string = $string."_".$key;
    }
    //取用户
    if (cache('get_all_users'.$string)){
        $get_all_users = cache('get_all_users'.$string);
    }else{
        $get_all_users = db('users')->column($field,$key);
        cache('get_all_users'.$string, $get_all_users, 300);
    }

    return $get_all_users;
}

function get_manager(){

    //取用户
    if (cache('get_manager')){
        $get_manager = cache('get_manager');
    }else{
        $map['status'] = 1;
        //$map['group_id'] = 0;  //业务员分组
        $get_manager = db('admin')->where($map)->column('uid, username');
        cache('get_manager', $get_manager, 300);
    }

    return $get_manager;
}