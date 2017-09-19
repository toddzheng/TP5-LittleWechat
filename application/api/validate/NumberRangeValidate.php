<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/19 0019
 * Time: 13:57
 */

namespace app\api\validate;


class NumberRangeValidate extends BaseValidate
{
    protected $rule = [
        'num' => 'isPositiveInteger|between:1,15'
    ];
}