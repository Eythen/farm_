<?php

namespace app\api\controller;


use app\api\service\UserToken;
use app\api\validate\TokenGet;

class Token extends Base
{
    public function getToken($code='',$type=0){
        (new TokenGet())->goCheck();
        $ut = new UserToken($code,$type);
        $res = $ut->get();
        return $res;
    }
}