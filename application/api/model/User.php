<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/21 0021
 * Time: 9:21
 */

namespace app\api\model;


class User extends  BaseModel
{
    public static function getUserByOpenId($openid){
        $user = self::where('openid','=',$openid)->find();
        return $user;
    }
    public static  function newUser($openid){
        $user = self::create(['openid'=>$openid]);
        return $user->id;
    }
}