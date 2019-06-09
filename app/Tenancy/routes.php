<?php
use Illuminate\Routing\Router;
// Admin::registerAuthRoutes();
// Admin::registerAuthRoutes();
Route::group(['domain' => 'admin888.shaibibi.com'], function (Router $router) {
	Admin::registerAuthRoutes();
	$router->get('/api/city',function(){
		$provinceId = request()->get('q');
		//return $provinceId;
		$id = \DB::table('district')->where('code',$provinceId)->select(['id'])->get()->toArray();
		
    	return \DB::table('district')->where('parent_id', $id[0]->id)->select([\DB::raw('code as id'),\DB::raw('name as text')])->get();
	});
});

Route::group([
	'domain' => 'admin888.shaibibi.com',
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
		$router->resource('goods','\App\Admin\Controllers\ProductsController');
		$router->resource('shops','ShopsController');
		$router->resource('banners',"BannerController");
		$router->resource('videos',"VideoController");
		$router->resource('agents','AgentController');
		$router->resource('scategory','sCategoryController');
		$router->resource('orders','OrderController');
		$router->resource('withdraw','TiXianController');
		$router->post('withdraw/do/{tixian}', 'TiXianController@withdraw')->name('tenancy.withdraw.do');
});
