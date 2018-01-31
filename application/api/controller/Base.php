<?php

namespace app\api\controller;


use think\Controller;

class Base extends Controller
{
    public function getCode(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&'
        .'appid='.config('wx.AppID').'&secret='.config('wx.AppSecret');
        $wxRes = cget($url);
        $wxRes = json_decode($wxRes,true);

        $curl = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$wxRes['access_token'];
        $data = json_encode(['page'=>'pages/my/center','scene'=>1]);
        $res = cpost($curl,$data);
        file_put_contents('1.jpg',$res);
    }
}