<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 15:25
 */

namespace app\api\controller\v1;
use \app\api\model\Banner as BannerModel;
use app\lib\exception\BannerException;
use app\api\validate\IDMustBePositiveInt;

class Banner
{
    public  function  banner($id){
        $validate = new IDMustBePositiveInt();
        $validate->goCheck();
        $banner = BannerModel::getBanner($id);
        if(!$banner){
            throw new BannerException();
        }
        return $banner;
    }
}