<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/19 0019
 * Time: 10:57
 */

namespace app\api\controller\v1;
use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;


class Category
{
    public  function  getAllCategory(){
        $categories = CategoryModel::all([],'img');
        if($categories->isEmpty()){
           throw new CategoryException();
        }
        return $categories;
    }

}