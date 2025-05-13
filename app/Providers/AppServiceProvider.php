<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Use the custom pagination view
        Paginator::defaultView('vendor.pagination.custom');

        // Use Bootstrap for the simple pagination view
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-5');
    }
}
