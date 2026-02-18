<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk akses Agency
 */
class CheckAgencyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->hasRole('agency')) {
            return $next($request);
        }

        abort(403, 'Unauthorized - Agency access required');
    }
}
