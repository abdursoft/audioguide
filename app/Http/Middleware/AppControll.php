<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppControll
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->header('App-Secret')){
            if($request->header('App-Secret') == env('APP_SECRET')){
                return $next($request);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid App-Secret, Please send correct one'
                ],400);
            }

        }else{
            return response()->json([
                'status' => false,
                'message' => 'App-Secret is missing in your request. Please send your request with App-Secret'
            ],400);
        }
    }
}
