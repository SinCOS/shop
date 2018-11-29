<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class CustomUserProvider implements UserProvider
{
    protected $model ;
    public function __construct($model){
        $this->model = $model;
    }
 public function retrieveById($identifier)
    {   $model = (new $this->model);
          return $model->newQuery()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    public function retrieveByToken($identifier, $token)
    {}

    public function updateRememberToken(Authenticatable $user, $token)
    {}

    public function retrieveByCredentials(array $credentials)
    {
        return (new $this->model)->where('username',$credentials['username'])->whereOr('mobile',$credentials['username'])->first();
        // 用$credentials里面的用户名密码去获取用户信息，然后返回Illuminate\Contracts\Auth\Authenticatable对象
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // dd($credentials);
     
        return \Hash::check($credentials['password'],$user->password);
        // 用$credentials里面的用户名密码校验用户，返回true或false
    }
}
