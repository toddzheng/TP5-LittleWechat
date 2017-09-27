<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/22 0022
 * Time: 10:58
 */

namespace app\api\service;


use app\api\model\Order as OrderModel;
use app\api\model\OrderFail;
use app\api\model\OrderProduct;
use app\api\model\UserAddress;
use app\api\model\Product;
use app\lib\exception\OrderException;
use app\lib\exception\UserExcpetion;
use think\Db;


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
        $i=0;
        foreach ($this->oProducts as $oProduct){
            $pStatus = $this->getProductStatus($oPID=$oProduct['product_id'], $oCount=$oProduct['count'], $products=$this->dProducts);
            $oStatus['orderPrice'] +=$pStatus['totalPrice'];
            $oStatus['totalCount'] +=$pStatus['counts'];
            $oStatus['pStatus'][$i]=$pStatus;
            ++$i;
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
                    //库存不足，，记录产品的Id，名字，数量，将订单记录在数据库后再抛出异常给客户端，便于后期订单数据分析
                    $orderFail = new OrderFail();
                    $orderFail->user_id = $this->uid;
                    $orderFail->product_id = $product['id'];
                    $orderFail->product_price = $product['price'];
                    $orderFail->product_name = $product['name'];
                    $orderFail->stock = $product['stock'];
                    $orderFail->count = $oCount;
                    $orderFail->save();
                    throw new OrderException($msg='订单中'.$product['name'].'的库存不足，请刷新后重新下单',$errorCode=80002);
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
        $snap['pStatus'] = $status['pStatus'];
        $snap['snapAddress'] =json_encode($this->getUserAddress());
        $snap['snapName'] = $this->dProducts[0]['name'];
        $snap['snapImg'] = $this->dProducts[0]['main_img_url'];
        if(count($this->dProducts)>1){
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
        Db::startTrans();
        try{
            $order = new OrderModel();

            $order_no = $this->createOrderNumber();
            $order->order_no = $order_no;
            $order->user_id = $this->uid;
            $order->total_price = $orderSnap['orderPrice'];
            $order->total_count = $orderSnap['totalCount'];
            $order->snap_img = $orderSnap['snapImg'];
            $order->snap_name = $orderSnap['snapName'];
            $order->snap_address = $orderSnap['snapAddress'];
            $order->snap_items = json_encode($orderSnap['pStatus']);
            $order->save();
            $order_id = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p){
                $p['order_id'] = $order_id;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $order_no,
                'order_id' => $order_id,
                'create_time' => $create_time
            ];
        }catch (\Exception $e){
            Db::rollback();
            throw $e;
        }
    }
    public static function createOrderNumber()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }
}