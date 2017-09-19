<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/19 0019
 * Time: 15:57
 */
return[
  'Appid'=>'wx6e053bbecd463383',
  'Appsecret'=>'fa81bd0e94790b7ce4144730e44d73a1',
  'login_url'=> "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
  'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",
];