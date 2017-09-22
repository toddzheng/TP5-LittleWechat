<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/20 0020
 * Time: 11:39
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{
    protected $hidden = [
        'delete_time', 'id','update_time','product_id'];
}