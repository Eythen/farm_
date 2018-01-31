<?php

namespace app\api\service;


use app\api\model\Order;
use app\lib\exception\OrderException;
use think\Loader;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class Refund
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

    public function refund(){
        $this->checkOrder();
        $wxOrderData = new \WxPayRefund();
        $wxOrderData->SetOut_trade_no($this->order_sn);
        $wxOrderData->SetTotal_fee($this->order_amount * 100);
        $wxOrderData->SetRefund_fee($this->order_amount * 100);
        $wxOrderData->SetOut_refund_no(\WxPayConfig::MCHID.date("YmdHis"));
        $wxOrderData->SetOp_user_id(\WxPayConfig::MCHID);
        $wxOrder = \WxPayApi::refund($wxOrderData);
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
            return false;
        }
        return true;
    }

    private function checkOrder(){
        $order = Order::get($this->order_id);
        if (!$order){
            throw new OrderException();
        }
        if (!Token::isValidOperate($order->user_id)){
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003,
            ]);
        }
        $this->order_sn = $order->order_sn;
        $this->order_amount = $order->order_amount;
    }

}