<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\JWTAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserWeb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        $token = JWTAuth::verifyToken($request->cookie('user_token'),false);

        // return response()->json($token);
        if($token && $token->role === 'user'){
            $request->headers->set('email',$token->email);
            $request->headers->set('io',$token->id);
            return $next($request);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ],401);
        }
    }
}
