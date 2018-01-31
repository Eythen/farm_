<?php

namespace app\lib\exception;


class UserAddressException extends BaseException
{
    public $code = 404;
    public $msg = '该收货地址不存在';
    public $errorCode = 90000;
}