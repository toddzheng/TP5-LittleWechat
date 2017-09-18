<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 17:27
 */

namespace app\lib\exception;
use think\exception\Handle;
use think\facade\Request;
use think\facade\Log;
use Exception;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    //返回客户端当前请求的url
    public  function  render(Exception $e)
    {
        if($e instanceof BaseException ){
            //如果是自定义的异常，返回错误信息
            $this->msg = $e->msg;
            $this->code = $e->code;
            $this->errorCode = $e->errorCode;
        }else{
            if(config('app_debug')){
                return parent::render($e);
            }else{
                $this->code = 500;
                $this->msg = '服务器内部错误';
                $this->errorCode = 999;
                $this->recordErrorLog($e);
            }
        }
        $request =Request::instance();
        $result = [
            'msg'=>$this->msg,
            'errorCode'=>$this->errorCode,
            'url'=>$request->url()
        ];


        return json($result,$this->code);
    }
    /*
     * 将异常写入日志
     */
    private function recordErrorLog(Exception $e)
    {
        $log = Log::instance();
        $log->init([
            'type'  =>  'File',
            'path'  =>  LOG_PATH,
            'level' => ['error']
        ]);
//        Log::record($e->getTraceAsString());
        $log->record($e->getMessage(),'error');
    }
}