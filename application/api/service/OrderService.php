<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/22 0022
 * Time: 10:58
 */

namespace app\api\service;


use app\api\model\UserAddress;
use app\api\model\Product;
use app\lib\exception\OrderException;
use app\lib\exception\UserExcpetion;


class OrderService
{
    //客户端传过来的商品列表
    protected $oProducts;
    //数据库中的对应客户端传过来的商品查询结果(主要是对比库存是否足够)
    protected $dProducts;
    //客户端传过来的用户id
    protected $uid;
    public  function placeOrder($uid,$oProducts){
        $this->uid = $uid;
        $this->oProducts = $oProducts;
        $this->dProducts = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        if(!$status){
            //如果status为false，则将订单号标记为-1，记录后抛出异常
            $status['order_id']=-1;
        }
        //记录订单
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        return $order;
    }

    protected function getProductsByOrder($oProducts){
        //用于存储订单中所有商品的id
        $oPid = [];
        foreach ($oProducts as $product){
            array_push($oPid,$product['product_id']);
        }
        $products = Product::all($oPid)->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();
        return $products;
    }
    private function getOrderStatus(){
        //预定义产品的状态数据结构
        $oStatus = [
          'orderPrice'  =>0,
         'totalCount'   =>0,
            //产品的状态，库存是否足够，足够的话，返回产品的相关信息
            'pStatus'   =>[]
        ];
        foreach ($this->oProducts as $oProduct){
            $pStatus = $this->getProductStatus($oPID=$oProduct['product_id'], $oCount=$oProduct['count'], $products=$this->dProducts);
            //如果某个产品超出库存，直接返回false，记录以后再抛出异常给客户端
            if(!$pStatus){
                return false;
            }
            $oStatus['orderPrice'] +=$pStatus['totalPrice'];
            $oStatus['totalCount'] +=$pStatus['counts'];
            array_push($oStatus['pStatusArray'], $pStatus);
        }
        return $oStatus;
    }
    private function getProductStatus($oPID,$oCount,$products){
        //预定义订单中单个产品的数据结构
            $pStatus = [
                //产品id
                'id' => null,
                //该商品总数量
                'counts' => 0,
                'price' => 0,
                'name' => '',
                'totalPrice' => 0,
                'main_img_url' => null
            ];
            //预定该单个产品的id为-1，最后判断最后是否为-1，如果为-1，说明订单中的这个产品不在数据库中存在，抛出一个异常。
            $pIndex = -1;
            $productNum = count($products);
            for ($i=0;$i<$productNum;$i++){
                if($oPID==$products[$i]['id']){
                    $pIndex = $i;
                }
            }
            if($pIndex==-1){
                //说明该产品在数据库中未找到，抛出异常
                throw new OrderException($mgs='订单中的商品不存在',$errorCode=80001);
            }
                //订单中的商品的具体信息对应的就是数据库中查出来的这个商品的信息
                $product = $products[$pIndex];
                if($product['stock']-$oCount<0){
                    //说明库存不够，抛出订单失败异常
//                    throw new OrderException($mgs='订单中存在库存不足的商品',$errorCode=80002);
                    //库存不足，不直接抛出异常，先返回false，将订单记录在数据库后再抛出异常给客户端，便于后期订单数据分析
                    return false;
                }
                $pStatus['id'] = $product['id'];
                $pStatus['name'] = $product['name'];
                $pStatus['counts'] = $oCount;
                $pStatus['price'] = $product['price'];
                $pStatus['main_img_url'] = $product['main_img_url'];
                $pStatus['totalPrice'] = $product['price'] * $oCount;
                $pStatus['haveStock'] = true;
                return $pStatus;


    }
    protected  function  snapOrder($status){
        $snap=[
          'orderPrice'=>0,
          'totalCount'=>0,
          'pStatus'=>[],
          'snapAddress'=>'',
          'snapName'=>'',
          'snapImg'=>'',
        ];
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        //二维数组
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] =json_encode($this->getUserAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        if(count($this->product)>1){
            $snap['snapName'] .='等';
        }
        return $snap;
    }

    protected function getUserAddress(){
        $userAddress = UserAddress::where('user_id','=',$this->uid)->find();
        if(!$userAddress){
            throw new UserExcpetion($msg='用户收货地址不存在，无法下单',$errorCode=20001);
        }
        return $userAddress->toArray();
    }

    protected function createOrder($orderSnap){

    }
}