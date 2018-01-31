<?php
/**
 * 参考地址 http://www.cnblogs.com/txw1958/p/weixin-js-sharetimeline.html
 * http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html  微信JS-SDK说明文档
 */

namespace app\wap\logic;

use think\Model;
/**
 * 分类逻辑定义
 * Class CatsLogic
 * @package Home\Logic
 */
class Jssdk extends Model
{
 
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }
  // 签名
  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "rawString" => $string,
      "signature" => $signature
      
    );
    return $signPackage; 
  }
// 随机字符串
  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }


    /**
     * 根据 access_token 获取 icket
     * @return type
     */
    public function getJsApiTicket(){        
        
        $ticket = cache('ticket');
        if(!empty($ticket))
            return $ticket;
        
        $access_token = $this->get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
        $return = httpRequest($url,'GET');
        $return = json_decode($return,1);        
        cache('ticket',$return['ticket'],7000);
        return $return['ticket'];
    }     
      
  
    /**
     * 获取 网页授权登录access token
     * @return type
     */
    public function getAccessToken(){
        //判断是否过了缓存期
        $access_token = cache('access_token');
        if(!empty($access_token))
            return $access_token;
        
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
        $return = httpRequest($url,'GET');
        $return = json_decode($return,1);
        cache('access_token',$return['access_token'],7000);        
        return $return['access_token'];
    }    
    
    // 获取一般的 access_token
    public function get_access_token(){
        //判断是否过了缓存期
        $wechat = db('wx_user')->find();
        $expire_time = $wechat['web_expires'];
        if($expire_time > time()){
           return $wechat['web_access_token'];
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$wechat['appid']}&secret={$wechat['appsecret']}";
        $return = httpRequest($url,'GET');
        $return = json_decode($return,1);
        $web_expires = time() + 7000; // 提前200秒过期
        db('wx_user')->where(array('id'=>$wechat['id']))->update(array('web_access_token'=>$return['access_token'],'web_expires'=>$web_expires));
        return $return['access_token'];
    }   
    
    /*
     * 向用户推送消息
     */
    public function push_msg($openid,$content){
        $access_token = $this->get_access_token();
        $url ="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";        
        $post_arr = array(
                        'touser'=>$openid,
                        'msgtype'=>'text',
                        'text'=>array(
                                'content'=>$content,
                            )
                        );
        $post_str = json_encode($post_arr,JSON_UNESCAPED_UNICODE);        
        $return = httpRequest($url,'POST',$post_str);
        $return = json_decode($return,true);        
    }

    /*
     * 向用户推送消息
     */
    public function push_msg_one(){
        $openid = 'oIAEy09SWisy6rxlHvOLeQMuFW7Y';
        $content = '';
        $template_id = 'bKG3nXYOVkQlhBEqLQCTc7VfAlu5_sL09g-fDDjm0XM';
        $access_token = $this->get_access_token();
        $url ="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";  
       // halt(222);      
  //"url":"http://weixin.qq.com/download",
        $post_str = '{
  "touser":"oIAEy09SWisy6rxlHvOLeQMuFW7Y",
  "template_id":"bKG3nXYOVkQlhBEqLQCTc7VfAlu5_sL09g-fDDjm0XM",
  "topcolor":"#FF0000",
  "url":"http://www.baidu.com",
  "data":{
    "first": {
    "value":"黄先生",
    "color":"#173177"
    },
    "keyword1":{
    "value":"06月07日 19时24分",
    "color":"#173177"
    },
    "keyword2":{
    "value":"人民币260.00元",
    "color":"#173177"
    },
    "keyword3":{
    "value":"杨清",
    "color":"#173177"
    },
    "keyword4":{
    "value":"158202",
    "color":"#173177"
    },
    "keyword5":{
    "value":"广州天河",
    "color":"#173177"
    },
    "remark":{
    "value":"06月07日19时24分",
    "color":"#173177"
    }
  }
}';

       
        //$post_str = json_encode($post_arr,JSON_UNESCAPED_UNICODE);
        /*$post_arr = json_decode($post_str,true);
        dump($post_arr); 
        halt(2); */    
        $return = cpost($url, $post_str);
        $return = json_decode($return,true);   
        dump($return);     
    }
 
}