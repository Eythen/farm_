<?php

namespace app\api\controller;


use app\api\validate\BookPlace;
use app\api\service\Token as TokenService;
use app\api\model\BookOrder as BookOrderModel;
use app\api\validate\PagingParameter;

class BookOrder extends Base
{
    public function placBookOrder(){
        (new BookPlace())->goCheck();
        $user_id = TokenService::getCurrentUid();
        $BookOrderModel = new BookOrderModel();
        $data = input('post.');
        return $BookOrderModel->place($user_id,$data);
    }

    public function getCount($type=1){
        $user_id = TokenService::getCurrentUid();
        $res = BookOrderModel::getCountByType($user_id,$type);
        return $res;
    }

    public function getByTypeOrder($type=1,$page=1,$size=10){
        (new PagingParameter())->goCheck();
        $user_id = TokenService::getCurrentUid();
        $list = BookOrderModel::getByType($user_id,$type,$page,$size);
        return $list;
    }

    public function getOne($order_id){
        $user_id = TokenService::getCurrentUid();
        $res = BookOrderModel::getOrderDetail($user_id,$order_id);
        return $res;
    }
}