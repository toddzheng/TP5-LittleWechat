<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15 0015
 * Time: 17:27
 */

namespace app\lib\exception;

class ParameterException extends BaseException
{
    public $code = 400;
    public $errorCode = 10000;
    public $msg = "invalid parameters";
    public function __construct($msg= "invalid parameters",$errorCode = 10000)
    {
        $this->msg = $msg;
        $this->errorCode = $errorCode;
    }
}