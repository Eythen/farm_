<?php

namespace app\lib\exception;


class CouponException extends BaseException
{
    public $code = 404;
    public $msg = '优惠券不存在';
    public $errorCode = 70000;
}