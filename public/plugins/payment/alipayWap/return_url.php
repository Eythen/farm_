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

class myreturnUrl extends Model
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

    function returnGet(){

		//require_once("alipay_config.php");
		require_once 'wappay/service/AlipayTradeService.php';


		$arr=$_GET;

		$alipaySevice = new AlipayTradeService($this->alipay_config); 
		$result = $alipaySevice->check($arr);

		/* 实际验证过程建议商户添加以下校验。
		1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
		2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
		3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
		4、验证app_id是否为该商户本身。
		*/
		if($result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

			//商户订单号

			$out_trade_no = trim($_GET['out_trade_no']);

			//支付宝交易号

			$trade_no = htmlspecialchars($_GET['trade_no']);
				
			//echo "验证成功<br />外部订单号：".$out_trade_no;
			$return = array('order_sn' => $out_trade_no, 'status' => 1);


			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
		    //验证失败
		    //echo "验证失败";
		    $return = array('order_sn' => '', 'status' => 1);
		}
		//dump($return);
	    return $return;
	}
}




?>
<title>支付宝手机网站支付接口</title>
	</head>
    <body>
    </body>
</html>