<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        // Application service providers are auto-discovered
        // from config/app.php providers array
    ])
    ->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware) {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'verify-midtrans-signature' => \App\Http\Middleware\VerifyMidtransSignature::class,
            'admin-access' => \App\Http\Middleware\CheckAdminAccess::class,
            'agency-access' => \App\Http\Middleware\CheckAgencyAccess::class,
            'visitor-access' => \App\Http\Middleware\CheckVisitorAccess::class,
            'unauthorized' => \App\Http\Middleware\HandleUnauthorized::class,
            'resource-access' => \App\Http\Middleware\HandleResourceAccess::class,
        ]);
    })
    ->withExceptions(function () {
        // Exception handling will be registered in app/Exceptions/Handler.php
    })
    ->create();
