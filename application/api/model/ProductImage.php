<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/20 0020
 * Time: 15:49
 */

namespace app\api\model;


class ProductImage extends  BaseModel
{
    protected $hidden = [
        'delete_time', 'img_id','product_id'];
    public function imgUrl(){
        return $this->belongsTo('Image','img_id','id');
    }
}