<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class AuthController extends BaseAuthController
{
	public function getLogin(){
		if(!\Auth::guard('admin')->guest()){
			return redirect('/home');
		}
		return view('shop.login');
	}
	public function postLogin(Request $request){
		$credentials = $request->only(['username','password','captcha']);
		$validator = \Validator::make($credentials,[
			'username' => ['required','string',
				Rule::exists('users','mobile')->where(function($query){
				$query->where('shop_id','>',0)->orWhere('is_agent','>',0);
			})
		], //shop_id,!0
			'password' => 'required',
			'captcha' =>'required|captcha',
		
		],[
			'exists' => '用户不存在'

		]);
		if($validator->fails()){
			return \Redirect::back()->withInput()->withErrors($validator);
		}
		unset($credentials['captcha']);
		if(\Auth::guard('admin')->attempt($credentials)){
			admin_toastr(trans('admin.login_successful'));
			return redirect()->intended('/home');
		}
		return \Redirect::back()->withInput()->withErrors(['username' => $this->getFailedLoginMessage()]);
	}

	protected function getFailedLoginMessage(){
		return \Lang::has('auth.failed') ?trans('auth.failed') : '账号或密码不匹配';
	}
}
