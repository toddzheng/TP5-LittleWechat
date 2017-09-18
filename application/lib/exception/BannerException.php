<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 17:20
 */

namespace app\lib\exception;

class BannerException extends BaseException
{
    //    http状态码
    public  $code = 400;
//    错误信息
    public  $msg = '请求的banner不存在';
//    自定义统一错误码
    public  $errorCode = 40000;
}