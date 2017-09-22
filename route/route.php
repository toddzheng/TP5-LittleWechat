<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');
Route::get('api/:version/product/recent', 'api/:version.product/getRencent');
Route::get('api/:version/banner/:id', 'api/:version.banner/banner');
Route::get('api/:version/category/all', 'api/:version.category/getAllCategory');

Route::get('api/:version/product/by_category/:id', 'api/:version.product/getAllInCategory');
Route::get('api/:version/product/:id', 'api/:version.product/getOne');

Route::get('api/:version/login/:code', 'api/:version.token/getToken');

// Token
Route::post('api/:version/token/user', 'api/:version.token/getToken');
Route::post('api/:version/address', 'api/:version.address/createOrUpdateAddress');
Route::get('api/:version/address/test', 'api/:version.address/test');
Route::get('api/:version/getAddress', 'api/:version.address/getUserAddress');

return [

];
