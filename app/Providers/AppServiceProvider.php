<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Vite;

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
        if (!str_contains(env('DATABASE_URL', ''), 'maria')) {
            \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        }
        
        $compiledViewPath = storage_path('framework/views');
        if (!is_dir($compiledViewPath)) {
            @mkdir($compiledViewPath, 0755, true);
        }


        // Define Gates for Authorization
        Gate::define('admin-access', function ($user) {
            return \DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.user_id', $user->id)
                ->where('roles.code', 'ADMIN')
                ->exists();
        });

        Gate::define('agency-access', function ($user) {
            return \DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.user_id', $user->id)
                ->where('roles.code', 'AGENCY')
                ->exists();
        });

        Gate::define('visitor-access', function ($user) {
            return \DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.user_id', $user->id)
                ->where('roles.code', 'VISITOR')
                ->exists();
        });
    }
}

