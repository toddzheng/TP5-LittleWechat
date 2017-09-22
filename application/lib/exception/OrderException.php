<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/22 0022
 * Time: 14:10
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    //    http状态码
    public  $code = 404;
//    错误信息
    public  $msg = '订单不存在，请检查ID';
//    自定义统一错误码
    public  $errorCode = 80000;

    public  function __construct($msg = '没有权限',$errorCode = 80000)
    {
        $this->msg = $msg;
        $this->errorCode = $errorCode;
    }
}