<?php

namespace app\api\service;


use app\api\model\Users;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    public function __construct($code,$type){
        $this->code = $code;
        if (!$type){
            $this->wxAppID = config('wx.AppID');
            $this->wxAppSecret = config('wx.AppSecret');
        }elseif ($type == 1){
            $this->wxAppID = config('wx.DcAppID');
            $this->wxAppSecret = config('wx.DcAppSecret');
        }
        $this->wxLoginUrl = sprintf(config('wx.LoginUrl'),$this->wxAppID,
            $this->wxAppSecret,$this->code);
    }

    public function get(){
        $res = cget($this->wxLoginUrl);
        $wxRes = json_decode($res,true);
        if (empty($wxRes)){
            throw new Exception('获取session_key及openId时异常，微信内部错误');
        }else{
            $loginFail = array_key_exists('errcode',$wxRes);
            if ($loginFail){
                $this->processLoginError($wxRes);
            }else{
                return $this->grantToken($wxRes);
            }
        }
    }

    private function grantToken($wxRes){
        $unionid = $wxRes['unionid'];
        $user = Users::getByUnionid($unionid);
        if ($user){
            $uid = $user->user_id;
            $mobile = $user->mobile;

            //登陆日志
            model('index/usersLogic', 'logic')->login_log($uid, $user->nickname, 'wx-s');
        }else{
            $uid = $this->newUser($unionid);
            $mobile = '';
        }

        $cacheValue = $this->prepareCahedValue($wxRes,$uid);
        $token = $this->saveToCache($cacheValue);
        return [
            'token' => $token,
            'mobile' => $mobile,
        ];
    }

    private function saveToCache($cacheValue){
        $key = self::generateToken();
        $value = json_encode($cacheValue);
        $expire_in = config('wx.token_expire_in');
        $res = cache($key,$value,$expire_in);
        if (!$res){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key;
    }

    private function prepareCahedValue($wxRes,$uid){
        $cachedValue = $wxRes;
        $cachedValue['uid'] = $uid;
        return $cachedValue;
    }

    private function newUser($unionid){
        $user = Users::create([
            'reg_time' => time(),
            'oauth' => 'wx',
            'unionid' => $unionid,
        ]);
        return $user->user_id;
    }

    private function processLoginError($wxRes){
        throw new WeChatException([
            'msg' => $wxRes['errmsg'],
            'errorCode' => $wxRes['errcode']
        ]);
    }
}