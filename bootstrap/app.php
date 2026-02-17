<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        // Application service providers are auto-discovered
        // from config/app.php providers array
    ])
    ->withMiddleware(function () {
        // Middleware will be registered in app/Http/Kernel.php
    })
    ->withExceptions(function () {
        // Exception handling will be registered in app/Exceptions/Handler.php
    })
    ->create();
