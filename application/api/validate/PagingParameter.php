<?php

namespace app\api\validate;


class PagingParameter extends Base
{
    protected $rule = [
        'page' => 'isPostiveInteger',
        'size' => 'isPostiveInteger'
    ];

    protected $message = [
        'page' => '分页参数必须是正整数',
        'size' => '分页参数必须是正整数'
    ];
}