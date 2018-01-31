<?php

namespace app\api\service;


use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    public static function generateToken(){
        //32个字符组成随机字符串
        $randChars = get_rand_str(32);
        //salt
        $salt = config('wx.token_salt');
        return md5($randChars.$salt);
    }

    public static function getCurrentTokenVar($key){
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars){
            throw new TokenException();
        }else{
            if (!is_array($vars)){
                $vars = json_decode($vars,true);
            }
            if (array_key_exists($key,$vars)){
                return $vars[$key];
            }else{
                throw new Exception('尝试获取的token变量并不存在');
            }
        }
    }

    public static function getCurrentUid(){
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    public static function isValidOperate($user_id){
        if (!$user_id){
            throw new Exception('检查user_id时必须传入一个被检查的user_id');
        }
        $currentOperateUID = self::getCurrentUid();
        if ($user_id == $currentOperateUID){
            return true;
        }
        return false;
    }
}