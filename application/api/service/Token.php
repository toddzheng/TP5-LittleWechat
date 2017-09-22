<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/21 0021
 * Time: 11:12
 */

namespace app\api\service;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\facade\Cache;
use \think\facade\Request;

class Token
{
    /*
     * 只有普通用户可以操作的权限
    */
    public static  function onlyAllowUserScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope== ScopeEnum::User){
            return true;
        }else{
        throw  new ForbiddenException();
        }
    }
    /*
     * 需要管理员以上的权限
    */
    public static  function onlyAllowCMSScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope >= ScopeEnum::CMS){
            return true;
        }else{
            throw  new ForbiddenException();
        }
    }
    /*
     * 需要普通用户以上的权限
    */
    public static  function needPrimaryScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope>= ScopeEnum::User){
            return true;
        }else{
            throw  new ForbiddenException();
        }
    }
    public  static function  getCurrentTokenVar($key){
        $request = Request::instance();
        $userToken = $request->header('token');
        $cache = Cache::instance();
        $cacheToken = $cache->get($userToken);
        if(!$cacheToken){
            throw new TokenException();
        }else{
            if (!is_array($cacheToken))
            {
                $cacheToken = json_decode($cacheToken, true);
            }
            if (array_key_exists($key, $cacheToken))
            {
                return $cacheToken[$key];
            }
            else
            {
                throw new Exception('尝试获取的Token变量并不存在');
            }
        }

    }
}