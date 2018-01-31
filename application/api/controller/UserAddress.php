<?php

namespace app\api\controller;

use app\api\service\Token as TokenService;
use app\api\model\UserAddress as UserAddressModel;
use app\api\validate\Address;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserAddressException;

class UserAddress extends Base
{
    public function getByUserId(){
        $user_id = TokenService::getCurrentUid();
        $res = UserAddressModel::all(['user_id'=>$user_id]);
        return $res;
    }

    public function createOrUpdateAddress(){
        (new Address())->goCheck();
        $user_id = TokenService::getCurrentUid();
        $data = input('post.');
        $res = UserAddressModel::createOrUpdateAddress($user_id,$data);
        if (!$res){
            throw new UserAddressException([
                'msg' => '收货地址设置错误',
                'error_code' => 90001
            ]);
        }
        return json(new SuccessMessage(),201);
    }

    public function deleteOne($id)
    {
        $res = UserAddressModel::destroy($id);
        if (!$res){
            throw new UserAddressException([
                'msg' => '收货地址删除失败',
                'error_code' => 90004
            ]);
        }
        return json(new SuccessMessage(),201);
    }
}