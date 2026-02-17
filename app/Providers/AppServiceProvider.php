<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set database string length for older MySQL versions
        if (!str_contains(env('DATABASE_URL', ''), 'maria')) {
            \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        }
        
        // Register compiled view path
        $compiledViewPath = storage_path('framework/views');
        if (!is_dir($compiledViewPath)) {
            @mkdir($compiledViewPath, 0755, true);
        }
    }
}
