<?php

namespace app\api\validate;


class KeywordParameter extends Base
{
    protected $rule = [
        'keyword' => 'require'
    ];
}