<?php

namespace app\lib\exception;


class GoodsMissException extends BaseException
{
    public $code = 404;
    public $msg = '请求的商品不存在';
    public $errorCode = 30000;
}