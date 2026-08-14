<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // <-- تأكد من وجود هذا السطر بالأعلى

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
            // السماح بظهور صفحة التوثيق على السيرفر
        Gate::define('viewApiDocs', function ($user = null) {
            return true; 
        });
    }
}
