<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 16:17
 */

namespace app\api\model;


class Banner extends BaseModel
{
    protected $hidden = ['id','delete_time','update_time'];
    public  static function  getBanner($id){

        $banner = self::with(['items','items.img'])->find($id);
        return $banner;
    }

    public  function  items(){
        return $this->hasMany('BannerItem','banner_id','id');
    }
}