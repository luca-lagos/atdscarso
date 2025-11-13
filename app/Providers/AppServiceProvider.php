<?php

namespace App\Providers;

use App\Models\Prestamo;
use App\Models\PrestamoBiblioteca;
use App\Observers\PrestamoBibliotecaObserver;
use App\Observers\PrestamoObserver;
use BezhanSalleh\PanelSwitch\PanelSwitch;
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
        Prestamo::observe(PrestamoObserver::class);
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->visible(fn(): bool => auth()->user()->hasRole('super-admin'))
                ->modalHeading('Paneles disponibles')
                ->modalWidth('sm')
                ->slideOver()
                ->panels([
                    'admin',
                    'docentes',
                    'alumnos',
                ])
                ->sort()
                ->labels([
                    'admin'    => 'Administración',
                    'docentes' => 'Docentes',
                    'alumnos'  => 'Alumnos',
                ])
                ->icons([
                    'admin'    => 'heroicon-o-cog-6-tooth',
                    'docentes' => 'heroicon-o-academic-cap',
                    'alumnos'  => 'heroicon-o-user',
                ], asImage: false)
                ->iconSize(15);
        });
    }
}
