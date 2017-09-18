<?php
/**
 * Created by PhpStorm.
 * User: 郑庆添
 * Date: 2017/9/18 0018
 * Time: 11:13
 */

namespace app\api\model;


use think\Model;

class BaseModel extends  Model
{
    protected  function  prefixImgUrl($value,$data){
        $finalUrl = $value;
        if($data['from']==1){
            $finalUrl = config('setting.img_prefix').$value;
        }
        return $finalUrl;
    }
}