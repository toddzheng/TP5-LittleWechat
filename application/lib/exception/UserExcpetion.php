<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/21 0021
 * Time: 13:44
 */

namespace app\lib\exception;


class UserExcpetion extends BaseException
{
    //    http状态码
    public  $code = 404;
//    错误信息
    public  $msg = '该用户不存在';
//    自定义统一错误码
    public  $errorCode = 20000;

    public  function __construct($msg = '该用户不存在',$errorCode = 20000,$code = 404)
    {
        $this->msg = $msg;
        $this->errorCode = $errorCode;
        $this->code = $code;
    }
}