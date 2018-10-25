<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\ErrorServe;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
     use AuthenticatesUsers;
     
    //
}
