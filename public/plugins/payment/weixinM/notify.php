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
die;

use think\Model;
/**
 * 支付 逻辑定义
 * Class 
 * @package Home\Payment
 */

class mynotifyUrl extends Model
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
        $this->alipay_config['return_url'] = SITE_URL.'/index.php/index/Payment/returnUrl/pay_code/alipayWap'; // 接收支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $this->alipay_config['notify_url'] = SITE_URL.'/index.php/index/Payment/notifyUrl/pay_code/alipayWap'; // 接收支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        //dump($this->alipay_config);
        //die;
                                      
    }  

    function notify(){

		//require_once("alipay_config.php");
		require_once 'wappay/service/AlipayTradeService.php';


		$arr=$_POST;
		$alipaySevice = new AlipayTradeService($this->alipay_config); 
		//$alipaySevice->writeLog(var_export($_POST,true));
		//$alipaySevice->writeLogGet(var_export($_GET,true));
		$result = $alipaySevice->check($arr);

		/* 实际验证过程建议商户添加以下校验。
		1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
		2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
		3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
		4、验证app_id是否为该商户本身。
		*/
		if($result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代

			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			
		    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//商户订单号

			$out_trade_no = $_POST['out_trade_no'];

			//支付宝交易号

			$trade_no = $_POST['trade_no'];

			//交易状态
			$trade_status = $_POST['trade_status'];


		    if($_POST['trade_status'] == 'TRADE_FINISHED') {

				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_amount与通知时获取的total_fee为一致的
					//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
		    }
		    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_amount与通知时获取的total_fee为一致的
					//如果有做过处理，不执行商户的业务程序			
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
		    }
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
		    $order_sn = $_POST['out_trade_no']; //商户系统的订单号，与请求一致。
		    update_pay_status($order_sn); // 修改订单支付状态

			return "success";		//请不要修改或删除
				
		}else {
		    //验证失败
		    return "fail";	//请不要修改或删除

		}
	}

}


?>

