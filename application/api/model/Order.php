<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/25 0025
 * Time: 9:18
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;

    public function getSnapItemsAttr($value){
        if(empty($value)){
            return '';
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value){
        if(empty($value)){
            return '';
        }
        return json_decode($value);
    }

    public static function getSummaryByPage($page=1,$pageSize=20){
        $pageData = self::order('id','desc')->paginate($pageSize,true,['page'=>$page]);
        if($pageData->isEmpty()){
            return[
              'current_page'=>$pageData->currentPage(),
                'data'      =>[]
            ];
        }
        $data = $pageData->hidden(['snap_items','snap_address'])->toArray();
        return[
            'current_page'=>$pageData->currentPage(),
            'data'      =>$data
        ];
    }
    public static function getSummaryByUserId($uid,$page=1,$pageSize=10){
        $orderData = self::where('user_id','=',$uid)->order('id','desc')
            ->paginate($pageSize,true,['page'=>$page]);
        if($orderData->isEmpty()){
            return[
                'current_page'=>$orderData->currentPage(),
                'data'      =>[]
            ];
        }
        $data = $orderData->hidden(['snap_items','snap_address','prepay_id'])->toArray();
        return[
            'current_page'=>$orderData->currentPage(),
            'data'      =>$data
        ];
        return $orderData;
    }

}