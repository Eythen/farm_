<?php

namespace app\api\controller;

use app\api\service\Token as TokenService;
use app\api\model\Order as OrderModel;
use app\api\validate\OrderPlace;
use app\index\logic\UsersLogic;
use app\home\controller\Express;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserAddressException;

class Order extends Base
{
    public function getByTpyeAll($type=0,$page=1,$size=3){
        $user_id = TokenService::getCurrentUid();
        $order = OrderModel::getByWhere($type,$user_id,$page,$size);
        return $order;
    }

    public function getOne($order_id){
        $user_id = TokenService::getCurrentUid();
        $order = OrderModel::getOrderDetail($order_id);
        return $order;
    }

    public function cancel($order_id){
        $user_id = TokenService::getCurrentUid();
        $logic = new UsersLogic();
        $data = $logic->cancel_order($user_id, $order_id);
        return $data;
    }

    public function orderConfirm($order_id){
        $user_id = TokenService::getCurrentUid();
        $data = confirm_order($order_id, $user_id);
        return $data;
    }

    public function logistics($order_id){
        $user_id = TokenService::getCurrentUid();
        $info = db('delivery_doc')->field('shipping_code,shipping_name,invoice_no,create_time')->where('order_id',$order_id)->find();
        $express = new Express();
        $info['res'] = $express->getOrderTraces($info['shipping_code'],$info['invoice_no']);
        return $info;
    }

    public function returns($order_id,$goods_id=0){
        $user_id = TokenService::getCurrentUid();
        $res = OrderModel::returns($user_id,$order_id,$goods_id);
        if (!$res){
            throw new UserAddressException([
                'msg' => '操作失败',
                'error_code' => 60001
            ]);
        }
        return json(new SuccessMessage(),201);
    }

    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $user_id = TokenService::getCurrentUid();
        $data = input('post.');
        $goods = input('post.goods/a');
        $OrderModel = new OrderModel();
        $res = $OrderModel->place($user_id,$data,$goods);
        if (!$res['status']){
            throw new OrderException([
                'msg' => '操作失败',
                'error_code' => 80003
            ]);
        }
        return json($res,201);
    }

    public function getOrderCount($type=1){
        $user_id = TokenService::getCurrentUid();
        $count = OrderModel::getOrderCount($type,$user_id);
        return $count;
    }
}