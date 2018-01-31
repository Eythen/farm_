<?php
/**
 * Description 		:	外部接口控制器
 * CreateDate 		:	2015/09/06
 * Creater 			:	weijiakuan
 * LastChangeDate 	:	2015/09/06
 * LastChanger 		:	weijiakuan
 */
namespace app\home\controller;
use think\Controller;

class Api extends Controller {

	public function curl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); //要访问的地址
		curl_setopt($ch, CURLOPT_REFERER, $url); //设置参考页
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //模拟用户使用的浏览器
	    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置超时限制防止死循环
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //获取的信息以文件流的形式返回
		$ouput = curl_exec($ch);
		if(curl_errno($ch)){
			echo 'Error：'.curl_error($ch);
		}
		curl_close($ch);
		return $ouput;
	}

    public function get_category(){
        $parent_id = input('get.parent_id'); // 商品分类 父id
        $list = db('goods_category')->where("parent_id = $parent_id")->select();

        foreach($list as $k => $v)
            $html .= "<option value='{$v['id']}'>{$v['name']}</option>";
        exit($html);
    }

    public function taobao() {
		if (request()->isPost()) {
			$mobile = input('mobile'); //获取电话
			header("content-type:text/html;charset=utf-8");
			//如果是固话，从百度抓取省份
			if(strchr($mobile, '-')){
				$mobile = substr($mobile, 0, strlen($mobile)-1); //删掉最后一位 ( 假设是 020-22222222 是，获取不到地址的 )
				$response = $this->curl("https://www.baidu.com/s?wd=$mobile"); //抓取数据
				preg_match_all('/<div class="op_mobilephone_r">([\s\S]+)<\/div>/U', $response, $ouput); //处理数据
				preg_match_all('/<span>([\s\S]+)<\/span>/U', $ouput[1][0], $ouput); //处理数据
				$province = reset(explode('&nbsp;', $ouput[1][1])); //返回省份
			}
			//如果是手机，从淘宝抓取省份
			else{
				$url = "https://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=" . $mobile . "&t=" . time(); //根据淘宝的数据库调用返回值
				$response = iconv('gbk', 'utf-8', $this->curl($url)); //抓取数据
				preg_match_all("/province:'(.+)'/U", $response, $ouput); //处理数据
				$province = $ouput[1][0]; //获取省份
				if(empty($province) || $province == '全国'){
					$response = $this->curl("https://www.baidu.com/s?wd=$mobile"); //抓取数据
					preg_match_all('/<div class="op_mobilephone_r">([\s\S]+)<\/div>/U', $response, $ouput); //处理数据
					preg_match_all('/<span>([\s\S]+)<\/span>/U', $ouput[1][0], $ouput); //处理数据
					$province = reset(array_filter(explode('&nbsp;', $ouput[1][1]))); //返回省份
				}
			}
			die($province);
		}
	}

	/**
	 * [getProvince 获取手机归属地]
	 * @param  [type] $mobile [手机号]
	 * @return [type]         [返回归属地]
	 */
	public function getProvince($mobile){
		//如果是固话，从百度抓取省份
		if(strchr($mobile, '-')){
			$mobile = substr($mobile, 0, strlen($mobile)-1); //删掉最后一位 ( 假设是 020-22222222 是，获取不到地址的 )
			$response = $this->curl("https://www.baidu.com/s?wd=$mobile"); //抓取数据
			preg_match_all('/<div class="op_mobilephone_r">([\s\S]+)<\/div>/U', $response, $ouput); //处理数据
			preg_match_all('/<span>([\s\S]+)<\/span>/U', $ouput[1][0], $ouput); //处理数据
			$province = reset(explode('&nbsp;', $ouput[1][1])); //返回省份
		}
		//如果是手机，从淘宝抓取省份
		else{
			$url = "https://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=" . $mobile . "&t=" . time(); //根据淘宝的数据库调用返回值
			$response = iconv('gbk', 'utf-8', $this->curl($url)); //抓取数据
			preg_match_all("/province:'(.+)'/U", $response, $ouput); //处理数据
			$province = $ouput[1][0]; //获取省份
			if(empty($province) || $province == '全国'){
				$response = $this->curl("https://www.baidu.com/s?wd=$mobile"); //抓取数据
				preg_match_all('/<div class="op_mobilephone_r">([\s\S]+)<\/div>/U', $response, $ouput); //处理数据
				preg_match_all('/<span>([\s\S]+)<\/span>/U', $ouput[1][0], $ouput); //处理数据
				$province = reset(array_filter(explode('&nbsp;', $ouput[1][1]))); //返回省份
			}
		}
		return $province;
	}
}
?>