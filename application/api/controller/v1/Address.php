<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 15:28
 */

namespace app\api\controller\v1;

use app\api\model\UserAddress;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessNotify;
use app\lib\exception\UserExcpetion;
use app\api\model\UserAddress as UserAddressModel;

class Address extends BaseController
{
    protected $beforeActionList = [
        'needPrimaryScope' => ['only' => 'createOrUpdateAddress,getUserAddress']
    ];

    public  function createOrUpdateAddress(){
        $validate = new AddressNew();
        $validate->goCheck();
        //根据token来获取用户的uid,如果用户不存在，则抛出异常
        //根据uid来获取用户数据，
        //获取用户提交过来的数据并且进行校验
        //如果用户信息以及存在，则进行更改，否则进行添加（目前针对每个用户只能添加一个地址）
        $uid =TokenService::getCurrentTokenVar('uid');
        if(!$uid){
            throw new UserExcpetion();
        }
        $dataArray = $validate->getDataByRule(input('post.'));
        $userAddress = UserAddressModel::where('user_id', $uid)->find();
        if($userAddress){
            //如果用户的地址存在，则进行更新动作，否则进行添加动作
            UserAddressModel::where('user_id', $uid)->update($dataArray);
        }else{
            //新增
            $dataArray['user_id'] = $uid;
            UserAddressModel::create($dataArray);
        }
        return json(new SuccessNotify(),201);
    }
    public function getAddress(){
        $uid =TokenService::getCurrentTokenVar('uid');
        $userAddress = UserAddressModel::where('user_id', $uid)->find();
        if(!$userAddress){
            throw new UserExcpetion($msg='用户地址不存在');
        }
        return $userAddress;
    }
}