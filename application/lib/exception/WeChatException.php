<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/19 0019
 * Time: 16:17
 */

namespace app\lib\exception;


use Throwable;

class WeChatException extends BaseException
{
    //    http状态码
    public  $code = 400;
    public $msg = '微信服务器接口调用失败';
    public $errorCode = 999;

    public  function __construct($msg= '微信服务器接口调用失败',$errorCode=999)
    {
        $this->msg = $msg;
        $this->errorCode = $errorCode;
    }
}