<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthServices;
use App\Utilisateur;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, AuthServices;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    protected $redirectTo = '/accueil.html';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(Auth::check())
        {
            redirect($this->redirectTo);
        }
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.connexion');
    }

    public function loginApi(Request $request){
      $this->login($request);
      return response()->json($this->generateNewTokens());
    }

    public function refreshToken(Request $request){
      try{
        Auth::loginUsingId($this->getIdFromRefreshToken($request->bearerToken()));
        return response()->json($this->generateNewTokens());
      }catch (\Exception $e){
        return response()->json([], 403);
      }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect()->route('login');
    }

    public function authenticated(Request $request,$user)
    {
        if($request->expectsJson()){
            return $user;
        }

        Auth::login($user);
        Cookie::queue(Cookie::make('login',$user->login,60*24*30,"/"));
        redirect()->route('home');
    }
}