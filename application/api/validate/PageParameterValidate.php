<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/26 0026
 * Time: 9:23
 */

namespace app\api\validate;


class PageParameterValidate extends BaseValidate
{
    protected $rule = [
        'page' => 'isPositiveInteger',
        'size' => 'isPositiveInteger'
    ];

    protected $message = [
        'page' => '分页参数必须是正整数',
        'size' => '分页参数必须是正整数'
    ];
}