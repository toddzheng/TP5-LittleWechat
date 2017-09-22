<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/19 0019
 * Time: 15:47
 */

namespace app\api\controller\v1;


use app\api\service\UserToken;
use app\api\validate\TokenGet;

class Token
{
    public  function  getToken($code){
        (new TokenGet())->gocheck();
        $userToken = new UserToken($code);
        $token = $userToken->get();
         return [
            'token'=>$token
        ];

    }
}