<?php

namespace app\api\validate;


class Address extends Base
{
    protected $rule = [
        'consignee' => 'require|isNotEmpty',
        'address' => 'require|isNotEmpty',
        'mobile' => 'require|isMobile',
        'is_default' => 'require|between:0,1'
    ];

    protected $message = [
        'consignee' => '收货人不能为空',
        'address' => '收货地址不能为空',
        'mobile.require' => '手机号码不能为空',
        'mobile.isMobile' => '手机号码格式错误',
        'is_default' => '是否为默认收货地址参数错误'
    ];
}