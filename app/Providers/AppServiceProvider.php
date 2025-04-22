<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Schema::defaultStringLength(191);
        Inertia::share([
            'auth' => function () {
                $user = Auth::user();

                return [
                    'user' => $user,
                    'permissions' => $user ? $user->getAllPermissions()->pluck('name') : [],
                ];
            },
        ]);
    }
}
