<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/19 0019
 * Time: 14:00
 */

namespace app\api\model;


class Product extends BaseModel
{
    protected $hidden = [
        'delete_time', 'main_img_id', 'pivot', 'from', 'category_id',
        'create_time', 'update_time','img_id'];
    public  function  getMainImgUrlAttr($value,$data){

        return $this->prefixImgUrl($value,$data);
    }
    public static function getRencent($num){
        $products = self::limit($num)->order('id desc')->select();
        return $products;
    }
    public static function getProductsByCategoryID($categoryID){
        $products = self::where('category_id','=',$categoryID)->select();
        return $products;
    }
    public  static function getProductDetail($productID){
        //Query
//        $product = self::with([
//            'imgs' => function($query){
//                $query->with(['imgUrl'])
//                    ->order('id', 'desc');
//            }
//        ])
//            ->with(['properties'])
//            ->find($productID);

        $product = self::with(['imgs.imgUrl'])->with(['properties'])->find($productID);

        return $product;

    }
    public  function properties(){
        return $this->hasMany('ProductProperty','product_id','id');
    }
    public function imgs(){
        return $this->hasMany('ProductImage','product_id')->order('order','desc');
    }
}