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

/*return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];*/

use think\Route;

// Route::rule('路由表达式', '路由地址', '请求类型', '路由参数(数组)', '变量规则(数组)');
// Route::rule('hello', 'sample/Test/hello', 'GET', ['https' => false]);
Route::get('/', 'index/Index/welcome');

// Banner相关
Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

// Theme相关
Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');
Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne');

// Product相关
Route::group('api/:version/product', function () {
    Route::get('/by_category', 'api/:version.Product/getAllInCategory');
    Route::get('/:id', 'api/:version.Product/getOne', [], ['id' => '\d+']);
    Route::get('/recent', 'api/:version.Product/getRecent');
});

// Category相关
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');

// Token相关
Route::post('api/:version/token/user', 'api/:version.Token/getToken');

// Address相关
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');

// Order相关
Route::group('api/:version/order', function () {
    Route::post('/', 'api/:version.Order/placeOrder');
});

// Pay相关
Route::group('api/:version/pay', function () {
    Route::post('/pre_order', 'api/:version.Pay/getPreOrder');
    Route::post('/notify', 'api/:version.Pay/receiveNotify');
});