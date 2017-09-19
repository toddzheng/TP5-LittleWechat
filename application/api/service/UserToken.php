<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/19 0019
 * Time: 15:54
 */

namespace app\api\service;
use app\lib\exception\WeChatException;
use think\Exception;

class UserToken
{
    protected $code;
    protected $wxAppid;
    protected $wxAppsecret;
    protected $wxLoginUrl;

    public  function __construct($code)
    {
        $this->code = $code;
        $this->wxAppid = config('wx.Appid');
        $this->wxAppsecret = config('wx.Appsecret');
        $this->wxLoginUrl = sprintf(
            config('wx.login_url'),
            $this->wxAppid,$this->wxAppsecret,$this->code);
    }
    public  function get(){
        $result = curl_get($this->wxLoginUrl);
        if($result){
            $rxResult = json_decode($result,1);
            $loginFail = array_key_exists('errcode',$rxResult);
            if($loginFail){
                $this->processLoginFail($rxResult);
            }else{
                return $this->grantToken($rxResult);
            }
        }else{
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        }
    }
    private  function  processLoginFail($rxResult){
        throw new WeChatException($rxResult['errmsg'],$rxResult['errcode']);
    }
    private function grantToken($rxResult){
        return $rxResult['openid'];
    }
}