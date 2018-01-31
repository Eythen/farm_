<?php

namespace app\api\validate;


class IDMustBePostiveInt extends Base
{
    protected $rule = [
        'id' => 'require|isPostiveInteger'
    ];

    protected $message = [
        'id' => 'id必须是正整数'
    ];
}