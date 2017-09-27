<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/22 0022
 * Time: 10:33
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderProductsValidate;
use app\api\service\Token as TokenService;
use app\api\service\OrderService;
use app\api\validate\PageParameterValidate;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;

class Order extends BaseController
{
    protected $beforeActionList = [
        'onlyAllowUserScope' => ['only' => 'orderPlace'],
        'needPrimaryScope' => ['only' => 'getSummaryByUser,getDetail'],
        'onlyAllowCMSScope' => ['only' => 'getSummary'],
    ];
    /*
     *下单
     * @返回的参数有 订单号(根据一定规则生成的唯一)，订单id（主键生成），下单时间
     */
    public  function orderPlace(){
        ( new OrderProductsValidate())->goCheck();
        //在参数后添加/才能接收数组格式的数据
        $products = input('post.products/a');
//        return 12;
        $uid = TokenService::getCurrentTokenVar('uid');
        $order = new OrderService();
        $orderStatus = $order->placeOrder($uid,$products);
        return $orderStatus;
    }
    /*
     * 分页
     * 获取所有用户订单简要信息(用于管理员查看)
     * */
    public  function getSummary($page=1,$size=20){
        (new PageParameterValidate())->goCheck();
        $pageOrders =OrderModel::getSummaryByPage($page,$size);
        return $pageOrders;
    }
    public function getSummaryByUser($page=1,$size=10){
        (new PageParameterValidate())->goCheck();
        $uid = TokenService::getCurrentTokenVar('uid');
        $pageOrders =OrderModel::getSummaryByUserId($uid,$page,$size);
        return $pageOrders;
    }
    /*
     * 获取订单详细信息,包括订单中的所有商品，地址之类的
     * */
    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }

}