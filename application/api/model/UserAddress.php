<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/21 0021
 * Time: 13:59
 */

namespace app\api\model;


use think\Model;

class UserAddress extends Model
{
    protected $hidden =['id', 'delete_time', 'user_id'];
}