<?php

namespace app\api\service;


use app\api\model\BookOrder;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class Pay
{
    private $order_id;
    private $order_sn;
    private $order_amount;

    function __construct($order_id)
    {
        if (!$order_id){
            throw new Exception("订单id不允许为空");
        }
        $this->order_id = $order_id;
    }

    public function pay($type){
        $this->checkOrder($type);

        if ($type == 1){
            $order = new OrderModel();
            $order->checkOrderStock($this->order_id);
        }

        return $this->makeWxPreOrder($this->order_amount,$type);
    }

    private function makeWxPreOrder($order_amount,$type){
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid){
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->order_sn);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($order_amount * 100);
        $wxOrderData->SetBody('九月新农园');
        $wxOrderData->SetOpenid($openid);
        if ($type == 1){
            $wxOrderData->SetNotify_url("https://m.septfarm.com/api/pay/notify");
        }elseif ($type == 2){
            $wxOrderData->SetAppid(config('wx.DcAppID'));
            $wxOrderData->SetNotify_url("https://m.septfarm.com/api/pay/notifydc");
        }

        return $this->getPaySignature($wxOrderData,$type);
    }

    private function getPaySignature($wxOrderData,$type)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
        }
        $this->recordPreOrder($wxOrder,$type);
        if ($type == 1){
            $signature = $this->sign($wxOrder,config('wx.AppID'));
        }elseif ($type == 2){
            $signature = $this->sign($wxOrder,config('wx.DcAppID'));
        }

        return $signature;
    }

    private function sign($wxOrder,$appid)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid($appid);
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time().mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('MD5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);
        return $rawValues;
    }

    private function recordPreOrder($wxOrder,$type){
        if ($type == 1){
            OrderModel::where('order_id', '=', $this->order_id)
                ->update(['prepay_id' => $wxOrder['prepay_id']]);
        }elseif ($type == 2){
            BookOrder::where('order_id','=',$this->order_id)
                ->update(['prepay_id' => $wxOrder['prepay_id']]);
        }
    }

    private function checkOrder($type){

        if ($type == 1){
            $order = OrderModel::get($this->order_id);
        }elseif ($type == 2){
            $order = BookOrder::get($this->order_id);
        }

        if (!$order){
            throw new OrderException();
        }
        if (!Token::isValidOperate($order->user_id)){
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003,
            ]);
        }
        if ($order->pay_status != 0){
            throw new OrderException([
                'msg' => '订单已支付过',
                'errorCode' => 80003,
            ]);
        }
        $this->order_sn = $order->order_sn;
        $this->order_amount = $order->order_amount;
        return true;

    }
}