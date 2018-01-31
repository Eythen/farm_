<?php

namespace app\api\validate;


class Count extends Base
{
    protected $rule = [
        'count' => 'isPostiveInteger|between:1,15'
    ];

    protected $message = [
        'count.isPostiveInteger' => 'count必须是正整数',
        'count.between' => 'count必须在1到15之前'
    ];
}