<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/19 0019
 * Time: 15:49
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule= [
        'code'=> 'require'
    ];

}