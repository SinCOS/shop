<?php

namespace App\Tenancy\Controllers;

use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Http\Request;



class AuthController extends BaseAuthController
{
	public function getLogin(){
		if(!\Auth::guard('admin')->guest()){
			return redirect(config('admin.router.prefix'));
		}
		return view('shop.login');
	}
	public function postLogin(Request $request){
		$credentials = $request->only(['username','password','captcha']);
		$validator = \Validator::make($credentials,[
			'username' => 'required',
			'password' => 'required',
			'captcha' =>'required|captcha'
		]);
		if($validator->fails()){
			return \Redirect::back()->withInput()->withErrors($validator);
		}
		unset($credentials['captcha']);
		if(\Auth::guard('admin')->attempt($credentials)){
			admin_toastr(trans('admin.login_successful'));
			return redirect()->intended(config('admin.router.prefix'));
		}
		return \Redirect::back()->withInput()->withErrors(['username' => $this->getFailedLoginMessage()]);
	}

	protected function getFailedLoginMessage(){
		return \Lang::has('auth.failed') ?trans('auth.failed') : '账号或密码不匹配';
	}
}
