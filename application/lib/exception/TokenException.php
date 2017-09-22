<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/21 0021
 * Time: 10:06
 */

namespace app\lib\exception;


class TokenException extends  BaseException
{
    public $code = 401;
    public $msg = 'Token已过期或无效Token';
    public $errorCode = 70000;

    public function __construct($msg= 'Token已过期或无效Token',$errorCode = 70000)
    {
        $this->msg = $msg;
        $this->errorCode = $errorCode;
    }
}