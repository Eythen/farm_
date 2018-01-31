<?php
/**
 *  微信支付插件
 * Author: yangqing
 * Date: 2017-09-13
 */

use think\Model;
/**
 * 支付 逻辑定义
 * Class 
 * @package Home\Payment
 */

class weixinM extends Model
{    
    public $tableName = 'plugin'; // 插件表        
    public $weixin_config = array();// 支付宝支付配置参数
    
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();
                
        $paymentPlugin = db('Plugin')->where("code='weixinM' and  type = 'payment' ")->find(); // 找到微信支付插件的配        
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化  

        //halt($config_value); 

        $this->weixin_config['appid'] = $config_value['appid']; // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        $this->weixin_config['mchid'] = $config_value['mchid']; // * MCHID：商户号（必须配置，开户邮件中可查看）
        $this->weixin_config['key'] = $config_value['key']; // KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）                                    
    }   

    /**
     * [get_code 微信H5支付]
     * @param  [type] $order        [订单信息]
     * @return [type]               [description]
     */
    function get_code($order){
         

       $userip = request()->ip(); //获得用户设备IP 自
       $appid = $this->weixin_config['appid'];//微信给的  
       $mch_id = $this->weixin_config['mchid'];//微信官方的  
       $key = $this->weixin_config['key'];//自己设置的微信商家key

      if($order['is_full'] == 2){
        if($order['pay_status'] == 2){
          $money= $order['amount80']*100;//支付金额  
          $out_trade_no = $order['order_sn'];//平台内部订单号  
        }
        else{
          $money= $order['amount20']*100;//支付金额  
          $out_trade_no = $order['order_sn2'];//平台内部订单号  
        }
      }
      else{
        $money= $order['order_amount']*100;//支付金额   
        $out_trade_no = $order['order_sn'];//平台内部订单号  
      }

       $nonce_str=MD5($out_trade_no);//随机字符串  
       $body = "H5支付商城订单";//内容  
       $total_fee = $money; //金额  
       $spbill_create_ip = $userip; //IP  
       $notify_url = SITE_URL.'/index.php/index/Payment/notifyUrl/pay_code/weixin'; //回调地址  
       $trade_type = 'MWEB';//交易类型 具体看API 里面有详细介绍  
       $scene_info ='{"h5_info":{"type":"Wap","wap_url":"http://m.septfarm.com","wap_name":"支付"}}';//场景信息 必要参数  

       $data = [
            'appid' => $appid,
            'body' => $body,
            'mch_id' => $mch_id,
            'nonce_str' => $nonce_str,
            'notify_url' => $notify_url,
            'out_trade_no' => $out_trade_no,
            'scene_info' => $scene_info,
            'spbill_create_ip' => $spbill_create_ip,
            'total_fee' => $total_fee,
            'trade_type' => $trade_type,
       ];
       ksort($data);
       $str = '';
       foreach ($data as $k => $v) {
           $str .="&".$k."=".$v;
       }
       $str = trim($str, '&');

       //$signA ="appid=$appid&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";  
  

       //$strSignTmp = $signA."&key=$key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML  是否正确  
       $strSignTmp = $str."&key=$key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML  是否正确 

       $sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写 
       $post_data = "<xml>  
                       <appid>$appid</appid>  
                       <body>$body</body>  
                       <mch_id>$mch_id</mch_id>  
                       <nonce_str>$nonce_str</nonce_str>  
                       <notify_url>$notify_url</notify_url>  
                       <out_trade_no>$out_trade_no</out_trade_no>  
                       <scene_info>$scene_info</scene_info>  
                       <spbill_create_ip>$spbill_create_ip</spbill_create_ip>  
                       <total_fee>$total_fee</total_fee>  
                       <trade_type>$trade_type</trade_type>  
                       <sign>$sign</sign>  
                   </xml>";//拼接成XML 格式  

          
       $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址   
       $dataxml = cpost($url, $post_data); //后台POST微信传参地址  同时取得微信返回的参数    
       $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组 
       //halt($objectxml);

       if($objectxml['return_code'] == "SUCCESS"){
            //$return = ['code' => 1, 'msg' => $objectxml['mweb_url']];
            //$this->redirect($objectxml['mweb_url']);
            header("Location: ".$objectxml['mweb_url']);
            exit;
       }
       else{
            //$return = ['code' => 0, 'msg' => $objectxml['return_msg']];
            $this->error($objectxml['return_msg']);
            //echo $objectxml['return_msg'];
       }
       //return $return;
       /*dump($post_data); 
       dump($dataxml); 
       halt($objectxml); */
    } 




   

    /**
     * 服务器点对点响应操作给支付接口方调用
     * 
     */
    function response()
    {                        
        //require_once("notify.php");  
        //$notify = new PayNotifyCallBack();
        //$notify->Handle(false); 
        //$data = (array)simplexml_load_string(file_get_contents("php://input"));
        //$notify->NotifyProcess($data, ''); 

    }
    
    /**
     * 页面跳转响应操作给支付接口方调用
     */
    function respond2()
    {
        // 微信扫码支付这里没有页面返回
    }

    



}