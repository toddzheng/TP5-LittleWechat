<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/22 0022
 * Time: 9:46
 */

namespace app\api\controller\v1;


use think\Controller;
use app\api\service\Token as TokenService;

class BaseController extends  Controller
{
    protected   function onlyAllowUserScope(){
        TokenService::onlyAllowUserScope();
    }
    protected function onlyAllowCMSScope(){
        TokenService::onlyAllowCMSScope();
    }
    protected function needPrimaryScope(){
        TokenService::needPrimaryScope();
    }
}