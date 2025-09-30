<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PhysicianMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->guard('api')->user()->isPhysician) {
            return $next($request);
        }
        return response()->json('Access denied');
    }
}
