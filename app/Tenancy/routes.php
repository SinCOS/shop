<?php
use Illuminate\Routing\Router;
// Admin::registerAuthRoutes();
// Admin::registerAuthRoutes();
Route::group(['domain' => 'hjt.lxrs.net'], function () {
    Admin::registerAuthRoutes();
});
Route::group([
	'domain' => 'hjt.lxrs.net',
	// 'namespace' => config('tenancy.route.namespace'),
	'prefix'        => config('tenancy.route.prefix'),
    'middleware'    => config('tenancy.route.middleware')
  	], 
    function (Router $router) {
    	$router->get('/','HomeController@index');
    	 $router->get('auth/login', 'AuthController@getLogin');
    	$router->post('auth/login', 'AuthController@postLogin');
		$router->resource('users', 'UsersController');
		$router->resource('category', '\App\Admin\Controllers\CategoryController');
		$router->resource('shops','ShopsController');
		$router->resource('banners',"BannerController");
		$router->resource('videos',"VideoController");
		$router->resource('agents','AgentController');
		$router->resource('scategory','sCategoryController');
});
