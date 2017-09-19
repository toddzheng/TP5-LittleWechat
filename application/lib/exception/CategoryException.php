<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/19 0019
 * Time: 11:06
 */

namespace app\lib\exception;
use app\lib\exception\BaseException;

class CategoryException extends  BaseException
{
    //    http状态码
    public  $code = 400;
//    错误信息
    public  $msg = '请求的种类不存在';
//    自定义统一错误码
    public  $errorCode = 50000;
}