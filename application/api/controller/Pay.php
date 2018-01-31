<?php

namespace app\api\controller;


use app\api\service\Pay as PayService;
use app\api\service\Refund;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;

class Pay extends Base
{
     public function getPreOrder($id='',$type=1){
         (new IDMustBePostiveInt())->goCheck();
         $pay = new PayService($id);
         return $pay->pay($type);
     }

    public function receiveNotify(){
        $notify = new WxNotify();
        $notify->Handle();
    }

    public function notifydc(){
        $notify = new WxNotify();
        $notify->Handle();
    }


    public function refund($order_id){
        $refund = new Refund($order_id);
        $res = $refund->refund();
        if (!$res){
            throw new OrderException([
                'msg' => '已经退款成功，请勿重复退款'
            ]);
        }
        return json(new SuccessMessage(),201);
    }

}