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
//二级域名绑定

//return [
//	 '__domain__'=>[
//        //'m'      => 'wap',
//        // 泛域名规则建议在最后定义
//        //'*.user'    =>  'user',
//       // '*'         => 'book',
//    ],
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//    // 这里采用配置方式定义路由 动态注册的方式一样有效
//
//
//
//
//
//];

use think\Route;
Route::get('api/banner/:id','api/Ad/getBanner',[],['id'=>'\d+']);
Route::get('api/banner/by_cat/:id','api/Ad/getOne');

Route::get('api/goods/by_cat/page/:id','api/Goods/getByCat');
Route::get('api/goods/by_keyword','api/Goods/getByKeyword');
Route::get('api/goods/:id','api/Goods/getOne',[],['id'=>'\d+']);
Route::get('api/goods/hot','api/Goods/getHot');

Route::get('api/users/get_code/:mobile','api/Users/getCode');
Route::post('api/users/user','api/Users/editUser');

Route::post('api/token/user','api/Token/getToken');

Route::get('api/coupon/all','api/Coupon/getCoupon');
Route::post('api/coupon/receive','api/Coupon/receive');
Route::post('api/coupon/by_type','api/Coupon/getByType');

Route::post('api/address/by_user','api/UserAddress/getByUserId');
Route::post('api/address/address','api/UserAddress/createOrUpdateAddress');
Route::delete('api/address/delete', 'api/UserAddress/deleteOne');

Route::post('api/order/all','api/Order/getByTpyeAll');
Route::post('api/order/one','api/Order/getOne');
Route::post('api/order/cancel','api/Order/cancel');
Route::post('api/order/confirm','api/Order/orderConfirm');
Route::post('api/order/logistics','api/Order/logistics');
Route::post('api/order/returns','api/Order/returns');
Route::post('api/order/place','api/Order/placeOrder');
Route::post('api/order/count','api/Order/getOrderCount');

Route::post('api/pay/pre_order','api/Pay/getPreOrder');
Route::post('api/pay/notify','api/Pay/receiveNotify');
Route::post('api/pay/refund','api/Pay/refund');

Route::get('api/book_combo/all','api/BookCombo/getAll');
Route::get('api/book_goods/all','api/BookGoods/getAll');

Route::post('api/book/place','api/BookOrder/placBookOrder');
Route::post('api/book/count','api/BookOrder/getCount');
Route::post('api/book/all','api/BookOrder/getByTypeOrder');
Route::post('api/book/one','api/BookOrder/getOne');

Route::get('api/base/code','api/Base/getCode');

