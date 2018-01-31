<?php

namespace app\api\validate;


class User extends Base
{
    protected $rule = [
        'nickName' => 'require',
        'avatarUrl' => 'require',
    ];
}