<?php

namespace App\Providers\Filament;

use App\Filament\Alumnos\Widgets\AlumnoDashboardWidget;
use App\Filament\Resources\PrestamoBibliotecas\PrestamoBibliotecaResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AlumnosPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('alumnos')
            ->path('alumnos')
            ->login()
            ->profile()
            ->brandName('ATDScarso - Alumnos')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                PrestamoBibliotecaResource::class,
            ])
            ->discoverResources(in: app_path('Filament/Alumnos/Resources'), for: 'App\Filament\Alumnos\Resources')
            ->discoverPages(in: app_path('Filament/Alumnos/Pages'), for: 'App\Filament\Alumnos\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Alumnos/Widgets'), for: 'App\Filament\Alumnos\Widgets')
            ->widgets([
                /*AccountWidget::class,
                FilamentInfoWidget::class,*/
                AlumnoDashboardWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
