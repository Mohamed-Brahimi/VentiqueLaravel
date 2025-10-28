<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

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
        // Share locale with all views and set app locale
        View::composer('*', function ($view) {
            $locale = session('locale', 'en');
            if (in_array($locale, ['en', 'fr', 'es'])) {
                App::setLocale($locale);
            }
        });
    }
}
