<?php
/**
 * 支付宝支付插件

 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: yangqing
 * Date: 2017-09-06
 */

use think\Model;
/**
 * 支付 逻辑定义
 * Class 
 * @package Home\Payment
 */

class alipayWap extends Model
{    
    public $tableName = 'plugin'; // 插件表        
    public $alipay_config = array();// 支付宝支付配置参数
    
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();
                
        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/service/AlipayTradeService.php';
        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';

         //require_once("lib/WxPay.Api.php");


        $paymentPlugin = db('Plugin')->where("code='alipayWap' and  type = 'payment' ")->find(); // 找到支付插件的配  
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化   

        $this->alipay_config['app_id'] = $config_value['app_id']; // * APPID：绑定支付的APPID（必须配置，）
        $this->alipay_config['merchant_private_key'] = $config_value['merchant_private_key']; // *商户私钥，您的原始格式RSA私钥
        $this->alipay_config['alipay_public_key'] = $config_value['alipay_public_key']; //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        $this->alipay_config['gatewayUrl'] = "https://openapi.alipay.com/gateway.do";
        $this->alipay_config['charset'] = "UTF-8";
        $this->alipay_config['sign_type'] = "RSA2";
        $this->alipay_config['return_url'] =  SITE_URL.'/index.php/index/Payment/returnUrl/pay_code/alipayWap'; // 接收支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $this->alipay_config['notify_url'] = SITE_URL.'/index.php/index/Payment/notifyUrl/pay_code/alipayWap'; // 接收支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        //dump($this->alipay_config);
        //die;
                                      
    }   

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $config_value    支付方式信息
     */
    function get_code($order, $config_value)
    {       
        //$notify_url = SITE_URL.'/index.php/index/Payment/notifyUrl/pay_code/weixin'; // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        
        if($order['is_full'] ==2){
            if($order['pay_status'] == 2){
                //商户订单号，商户网站订单系统中唯一订单号，必填
                $out_trade_no = $order['order_sn'];

                //付款金额，必填
                $total_amount = $order['amount80'];

                //商品描述，可空
                $body = "支付订单：".$order['order_sn'];
            }
            else{
                //商户订单号，商户网站订单系统中唯一订单号，必填
                $out_trade_no = $order['order_sn2'];

                //付款金额，必填
                $total_amount = $order['amount20'];

                //商品描述，可空
                $body = "支付订单：".$order['order_sn2'];
            }
        }
        else{
            //商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = $order['order_sn'];

            //付款金额，必填
            $total_amount = $order['order_amount'];

            //商品描述，可空
            $body = "支付订单：".$order['order_sn'];

        }

        //订单名称，必填
        $subject = '支付宝支付';


//halt($this->$alipay_config);
        //超时时间
        $timeout_express="1m";



        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);
        $payResponse = new AlipayTradeService($this->alipay_config);
        $result=$payResponse->wapPay($payRequestBuilder,$this->alipay_config['return_url'],$this->alipay_config['notify_url']);


        return ;
    }    
    /**
     * 服务器点对点响应操作给支付接口方调用
     * 
     */
    function response()
    {                        
        require_once("notify_url.php");  
        $my = new mynotifyUrl();
        $r = $my->notify();
        echo $r;
        /*$notify = new PayNotifyCallBack();
        $notify->Handle(false); */
        //$data = (array)simplexml_load_string(file_get_contents("php://input"));
        //$notify->NotifyProcess($data, ''); 

    }
    
    /**
     * 页面跳转响应操作给支付接口方调用
     */
    function respond2()
    {
        require_once("return_url.php"); 
        $my = new myreturnUrl();
        $r = $my->returnGet();
        //dump($r);
        return $r;
    }

    function pay($order){
        if($order['is_full'] ==2){
            if($order['pay_status'] == 2){
                //商户订单号，商户网站订单系统中唯一订单号，必填
                $out_trade_no = $order['order_sn'];

                //付款金额，必填
                $total_amount = $order['amount80'];

                //商品描述，可空
                $body = "支付订单：".$order['order_sn'];
            }
            else{
                //商户订单号，商户网站订单系统中唯一订单号，必填
                $out_trade_no = $order['order_sn2'];

                //付款金额，必填
                $total_amount = $order['amount20'];

                //商品描述，可空
                $body = "支付订单：".$order['order_sn2'];
            }
        }
        else{
            //商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = $order['order_sn'];

            //付款金额，必填
            $total_amount = $order['order_amount'];

            //商品描述，可空
            $body = "支付订单：".$order['order_sn'];
        }
        //订单名称，必填
        $subject = '支付宝支付';

        //超时时间
        $timeout_express="1m";

        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);

        $payResponse = new AlipayTradeService($config);
        $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        return ;
    }

    



}