<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/26 0026
 * Time: 15:55
 */

namespace app\lib\exception;


class PayException extends BaseException
{
    //    http状态码
    public  $code = 400;
//    错误信息
    public  $msg = '支付失败';
//    自定义统一错误码
    public  $errorCode = 90000;

    public  function __construct($msg = '支付失败',$errorCode = 90000)
    {
        $this->msg = $msg;
        $this->errorCode = $errorCode;
    }
}