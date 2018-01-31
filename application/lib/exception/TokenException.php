<?php
;;
/**
 * Created by PhpStorm.
 * User: 那夏
 * Date: 2018/1/5
 * Time: 11:58
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code = 401;
    public $msg = 'Token已经过期或无效Token';
    public $errorCode = 10001;
}