<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;

use App\Models\User;

class Authenticate 
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        try {
            // prevent multiple tokens per user
            $cekAuth = User::where([
                ['id', auth()->payload()["uid"]],
                ['expiredToken', auth()->payload()["expiredToken"]]
            ])->exists();     
            
            if(!$cekAuth){
                return response()->json(['error' => "user tak dikenal"], 401);                 
            }      
            
            // prevent fake jwt from another server jwt issued
            if(auth()->payload()["domain"] != $_SERVER['SERVER_NAME']){
                return response()->json(['error' => "token tak dikenal"], 401);
            }
            
            return $next($request);
        
        } catch (TokenExpiredException $e) {        
            return response()->json(['error' => 'token was expired.'], 401);        
        } 
        
    }
}
