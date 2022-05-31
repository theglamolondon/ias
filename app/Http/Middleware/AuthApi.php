<?php

namespace App\Http\Middleware;

use App\Services\AuthServices;
use Closure;
use Illuminate\Http\Request;

class AuthApi
{
  use AuthServices;
  /**
   * Handle an incoming request from react fontend.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next)
  {
    try{
      $token = $request->bearerToken();

      if($token){
        if(!$this->verifyToken($request->bearerToken())){
          return response()->json(["message" => "token invalide"], 403);
        }
      }else{
        return response()->json(["message" => "token mismatch"], 401);
      }

      return $next($request);
    }catch (\Exception $e){
      return response()->json(["message" => $e->getMessage()], 403);
    }

  }
}
