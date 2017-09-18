<?php
/**
 * Created by PhpStorm.
 * User: 郑庆添
 * Date: 2017/9/18 0018
 * Time: 11:35
 */

namespace app\api\model;


class Image extends BaseModel
{
    protected $hidden = ['delete_time', 'id', 'from','update_time'];
    public  function  getUrlAttr($value,$data){

        return $this->prefixImgUrl($value,$data);
    }
}