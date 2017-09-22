<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/22 0022
 * Time: 9:54
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    //    http状态码
    public  $code = 400;
//    错误信息
    public  $msg = '没有权限';
//    自定义统一错误码
    public  $errorCode = 10001;

    public  function __construct($msg = '没有权限',$errorCode = 10001)
    {
        $this->msg = $msg;
        $this->errorCode = $errorCode;
    }
}