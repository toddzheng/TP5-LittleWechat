<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/19 0019
 * Time: 15:54
 */

namespace app\api\service;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

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
            $wxResult = json_decode($result,1);
            $loginFail = array_key_exists('errcode',$wxResult);
            if($loginFail){
                $this->processLoginFail($wxResult);
            }else{
                return $this->grantToken($wxResult);
            }
        }else{
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        }
    }
    private  function  processLoginFail($wxResult){
        throw new WeChatException($wxResult['errmsg'],$wxResult['errcode']);
    }
    private function grantToken($wxResult){
        //拿到openid，先去数据库看是否有该用户
        //如果有，生成新的token，如果没有，加入新用户，并生成新的token
        //生成的令牌，保存到缓存中（session）活动redis中，并将令牌返回给用户
        //缓存格式如下：key：token value：wxResult，uid, scope（权限）
        $openid =  $wxResult['openid'];
        $user = UserModel::getUserByOpenId($openid);
        if($user){
            $uid = $user->id;
        }else{
            $uid = UserModel::newUser($openid);
        }
        $cacheValue = $this->prepareCacheValue($wxResult,$uid);
        $token = $this->saveToCache($cacheValue);
        return $token;

    }
    protected function prepareCacheValue($wxResult,$uid){
        $cacheValue = $wxResult;
        $cacheValue['uid'] = $uid;
        $cacheValue['scope'] = ScopeEnum::User;
        return $cacheValue;

    }
    protected  function  saveToCache($cacheValue){
        $token = $this->generateToken();
        $value = json_encode($cacheValue);
        $expire_in = config('setting.token_expire_in');
        $cacheResult = cache($token,$value,$expire_in);
        if(!$cacheResult){
            $msg = '服务器缓存异常';
            $errorCode = 1005;
            throw new TokenException($msg,$errorCode);
        }else{
            return $token;
        }

    }
    protected  function generateToken(){
    //产生32个随机字符串
        $length = 32;
        $randChars = generateRadomString($length);
        //系统时间戳
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //salt 盐
        $salt = config('setting.token_salt');

        return md5($randChars . $timestamp . $salt);
}
}