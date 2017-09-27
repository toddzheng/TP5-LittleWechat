<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/26 0026
 * Time: 11:02
 */

namespace app\api\controller\v1;


use app\api\service\PayService;
use app\api\validate\IDMustBePositiveInt;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'onlyAllowUserScope' => ['only' => 'getPreOrder'],
        'needPrimaryScope' => ['only' => 'getSummaryByUser,getDetail'],
        'onlyAllowCMSScope' => ['only' => 'getSummary'],
    ];
    //支付，需要传递的参数有:
    //订单自增长的id
    public  function  getPreOrder($id){
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }
}