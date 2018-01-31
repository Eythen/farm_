<?php

namespace app\api\controller;


use app\api\validate\User;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;
use app\wap\controller\Alidayu;
use app\api\model\Users as UsersModel;
use app\api\service\Token as TokenService;

class Users extends Base
{
    public function getCode($mobile){
        $send = new Alidayu();
        $send->sendcode($mobile);
        $code = $send->code;
        return [
            'code' => $code,
        ];
    }

    public function editUser($nickName='',$avatarUrl=''){
        (new User())->goCheck();
        $user_id = TokenService::getCurrentUid();
        $user = UsersModel::get($user_id);
        if (!$user){
            throw new UserException();
        }
        $user->nickname = $nickName;
        $user->head_pic = $avatarUrl;
        $user->save();
        return json(new SuccessMessage(),201);
    }



}