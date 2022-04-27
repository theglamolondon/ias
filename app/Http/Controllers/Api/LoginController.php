<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 24/10/2021
 * Time: 20:07
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;


    public function username()
    {
        return 'login';
    }

    public function authenticated(Request $request,$user)
    {
        Auth::login($user);
        return $user;
    }
}