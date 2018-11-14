<?php

use Illuminate\Routing\Router;
Route::group(['domain' => 'business.lxrs.net'], function () {
    Admin::registerAuthRoutes();
});
Admin::registerAuthRoutes();

Route::group([
    'domain' => 'business.lxrs.net',
    'prefix' => '/api/v1',
     'namespace'     => config('admin.route.namespace'),

],function(Router $router){
    $router->get('/tpl','TplController@show');
});
Route::group([
    'domain' => 'business.lxrs.net',
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
   
    $router->resource('category', 'CategoryController');
    $router->get('setting','ShopController@edit');//->only('edit,show');
    $router->put('setting','ShopController@update');
    $router->resource('videos','VideoController');
    $router->resource('banners','BannerController');
    $router->get('auth/login', 'AuthController@getLogin');
    $router->post('auth/login', 'AuthController@postLogin');
    $router->get('products', 'ProductsController@index');
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    $router->get('products/{id}/edit', 'ProductsController@edit');
    $router->put('products/{id}', 'ProductsController@update');
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.orders.ship');
    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')->name('admin.orders.handle_refund');
    $router->get('coupon_codes', 'CouponCodesController@index');
    $router->post('coupon_codes', 'CouponCodesController@store');
    $router->get('coupon_codes/create', 'CouponCodesController@create');
    $router->get('coupon_codes/{id}/edit', 'CouponCodesController@edit');
    $router->put('coupon_codes/{id}', 'CouponCodesController@update');
    $router->delete('coupon_codes/{id}', 'CouponCodesController@destroy');


    $router->post('upload/editor', 'UploadController@uploadByEditor');
    $router->post('upload/file-input', 'UploadController@uploadByFileInput')->name('upload.file-input');
});