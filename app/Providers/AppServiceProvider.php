<?php

namespace App\Providers;

use App\Models\PrestamoBiblioteca;
use App\Observers\PrestamoBibliotecaObserver;
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
        /* if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }*/
        PrestamoBiblioteca::observe(PrestamoBibliotecaObserver::class);
    }
}
