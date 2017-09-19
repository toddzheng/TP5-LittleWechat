<?php
/**
 * Created by PhpStorm.
 * User: 郑庆添
 * Date: 2017/9/19 0019
 * Time: 11:09
 */

namespace app\api\model;


class Category extends  BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'create_time'];
    public  function  img(){
        return $this->belongsTo('Image','topic_img_id','id');
    }
}