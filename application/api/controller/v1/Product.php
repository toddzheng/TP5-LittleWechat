<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/19 0019
 * Time: 12:15
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;
use app\api\validate\NumberRangeValidate;
use app\api\model\Product as ProductModel;
use app\lib\exception\CategoryException;
use app\lib\exception\ProductException;

class Product
{
    public  function getRencent($num=15){
        (new NumberRangeValidate())->goCheck();
        $products = ProductModel::getRencent($num);
        if($products->isEmpty()){
            throw new ProductException();
        }
        return $products;
    }
    public  function  getAllInCategory($id){
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if($products->isEmpty()){
            throw new CategoryException();
        }
        return $products;
    }
    public  function getOne($id){
        (new IDMustBePositiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }
}