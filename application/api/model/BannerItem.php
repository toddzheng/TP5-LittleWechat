<?php
/**
 * Created by PhpStorm.
 * User: 郑庆添
 * Date: 2017/9/18 0018
 * Time: 11:18
 */

namespace app\api\model;


class BannerItem extends BaseModel
{
    protected $hidden = ['id', 'img_id', 'banner_id', 'delete_time','key_word','type','update_time'];
//    protected $visible = ['banner_id', 'img_id'];
    public  function  img(){
        return $this->belongsTo('Image','img_id','id');
    }
}