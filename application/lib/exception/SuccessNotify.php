<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/21 0021
 * Time: 14:38
 */

namespace app\lib\exception;


class SuccessNotify extends BannerException
{
    public $code = 201;
    public $msg = 'ok';
    public $errorCode = 0;
}