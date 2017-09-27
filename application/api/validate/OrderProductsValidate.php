<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/22 0022
 * Time: 10:42
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;

class OrderProductsValidate extends  BaseValidate
{
    //客户端传过来的商品的参数列表
//    protected $oProducts = [
//        [
//            'product_id' => 1,
//            'count' => 3
//        ],
//        [
//            'product_id' => 2,
//            'count' => 3
//        ],
//        [
//            'product_id' => 3,
//            'count' => 3
//        ]
//    ];
    protected $rule = [
        'products'=> 'checkProducts'
    ];
    protected $singleProductRule = [
        'count'=> 'require|isPositiveInteger',
        'product_id'=> 'require|isPositiveInteger',
    ];

    protected function checkProducts($values){
        if(! is_array($values)){
            throw new ParameterException($msg='商品参数不合法',$errorCode=10002);
        }
        if(empty($values)){
            throw new ParameterException($msg='商品参数不能为空',$errorCode=10003);
        }
        foreach ($values as $p){
            $this->checkProduct($p);
        }
        return true;
    }
    protected function checkProduct($p){
        $valite = new BaseValidate($this->singleProductRule);
        $result = $valite->check($p);
        $re = 1;

        if(!$result){
            throw new ParameterException($mgs='商品参数错误');
        }
    }
}