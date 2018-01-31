<?php
namespace app\wap\validate;
use think\Validate;
class User extends Validate
{
    protected $rule = [
        'nickname' => 'unique:users',
        'mobile' => 'unique:users|require',
        'password' => 'require',
    ];

    protected $message = [
        'nickname.unique' => '该昵称已被注册！',
        'mobile.require' => '手机号码不得为空！',
        'mobile.unique' => '该手机号码已被注册！',
        'password.require' => '密码不得为空！',
    ];

    protected $scene = [
        'add' => ['mobile','password'],
        'setup' => ['nickname'],
    ];
}
