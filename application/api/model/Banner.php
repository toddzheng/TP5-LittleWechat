<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 16:17
 */

namespace app\api\model;


class Banner
{
    public  static function  getBanner($id){

        $result = $id/2;
        return $result;
    }
}