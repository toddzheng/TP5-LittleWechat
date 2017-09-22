<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/22 0022
 * Time: 10:33
 */

namespace app\api\controller\v1;


use app\api\validate\OrderProductsValidate;
use app\api\service\Token as TokenService;
use application\api\service\OrderService;

class Order extends BaseController
{
    protected $beforeActionList = [
        'onlyAllowUserScope' => ['only' => 'createOrUpdateAddress,getUserAddress'],
        'needPrimaryScope' => ['only' => 'createOrUpdateAddress,getUserAddress'],
    ];
    public  function orderPlace(){
        ( new OrderProductsValidate())->goCheck();
        //在参数后添加/才能接收数组格式的数据
        $products = input('post.products/a');
        $uid = TokenService::getCurrentTokenVar('uid');
        $order = new OrderService();
        $orderStatus = $order->placeOrder($uid,$products);
        return $orderStatus;
    }
}