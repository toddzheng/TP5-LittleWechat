<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/19 0019
 * Time: 14:06
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    //    http状态码
    public  $code = 400;
//    错误信息
    public  $msg = '请求的商品不存在';
//    自定义统一错误码
    public  $errorCode = 60000;
}