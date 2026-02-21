<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleResourceAccess
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
        
        $user = Auth::user();
        
        // Check if user is trying to access someone else's payment/order
        if ($request->route('orderId')) {
            $orderId = $request->route('orderId');
            
            // For payment routes, check if order belongs to authenticated user
            if ($request->routeIs('payment.method') || $request->routeIs('orders.show') || $request->routeIs('orders.dispute')) {
                $order = \App\Models\Order::find($orderId);

                if (!$order || $order->visitor_user_id !== $user->id) {
                    return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
                }
            }
        }
        
        return $next($request);
    }
}
