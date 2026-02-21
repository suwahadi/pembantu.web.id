<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleUnauthorized
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
        // If user is not authenticated, redirect to login
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan login terlebih dahulu.');
        }

        // If user is authenticated but trying to access someone else's resource
        // This will be handled by specific route middleware or controller logic
        return $next($request);
    }
}
