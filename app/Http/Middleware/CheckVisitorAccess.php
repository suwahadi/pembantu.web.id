<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk akses Visitor
 */
class CheckVisitorAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->hasRole('visitor')) {
            return $next($request);
        }

        abort(403, 'Unauthorized - Visitor access required');
    }
}
