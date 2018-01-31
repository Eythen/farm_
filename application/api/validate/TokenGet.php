<?php

namespace app\api\validate;


class TokenGet extends Base
{
    protected $rule = [
        'code' => 'require|isNotEmpty'
    ];

    protected  $message = [
        'code.require' => '必须传入code',
        'code.isNotEmpty' => 'code不能为空'
    ];
}