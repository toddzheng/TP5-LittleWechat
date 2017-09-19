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
        'create_time', 'update_time'];
    public static function getRencent($num){
        $products = self::limit($num)->order('id desc')->select();
        return $products;
    }
    public static function getProductsByCategoryID($categoryID){
        $products = self::where('category_id','=',$categoryID)->select();
        return $products;
    }
    public  static function getProductDetail($productID){
        $product = self::where('id','=',$productID)->find();
        return $product;

    }
}