<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15 0015
 * Time: 15:42
 */

namespace app\api\validate;
use app\lib\exception\ParameterException;
use think\facade\Request;
use think\Validate;

class BaseValidate extends  Validate
{
    public  function  goCheck(){
        $request = Request::instance();
        $params = $request->param();
        if(!$this->check($params)){
            $exception = new ParameterException();
            $exception->msg = $this->error;
            throw $exception;
        }else{
            return true;
        }
    }
}