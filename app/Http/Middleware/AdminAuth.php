<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\JWTAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(empty($request->header('Authorization')) || !$request->header('Authorization') || $request->header('Authorization') == ''){
            $token = JWTAuth::verifyToken($request->cookie('admin_token'),false);
        }else{
            $token = JWTAuth::verifyToken($request->header('Authorization'),false);
        }

        // return response()->json($token);
        if($token && $token->role === 'admin'){
            $request->headers->set('email',$token->email);
            $request->headers->set('id',$token->id);
            return $next($request);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ],401);
        }
    }
}
