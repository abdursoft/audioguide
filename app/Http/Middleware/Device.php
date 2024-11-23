<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DeviceController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Device
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        (new DeviceController)->device($request);
        return $next($request);
    }
}
