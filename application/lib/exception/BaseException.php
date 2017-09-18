<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 17:12
 */

namespace app\lib\exception;


use think\Exception;

class BaseException extends Exception
{
//    http状态码
    public  $code = 400;
//    错误信息
    public  $msg = 'invalidate message';
//    自定义统一错误码
    public  $errorCode = 10000;

}
