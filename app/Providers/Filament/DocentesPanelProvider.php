<?php

namespace App\Providers\Filament;

use App\Filament\Docentes\Widgets\DocenteDashboardWidget;
use App\Filament\Resources\PrestamoBibliotecas\PrestamoBibliotecaResource;
use App\Filament\Resources\Prestamos\PrestamoResource;
use App\Filament\Resources\TurnosSalas\TurnosSalaResource;
use App\Filament\Resources\TurnosTvs\TurnosTvResource;
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

class DocentesPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('docentes')
            ->path('docentes')
            ->login()
            ->profile()
            ->brandName('ATDScarso - Docentes')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                TurnosSalaResource::class,
                TurnosTvResource::class,
                PrestamoBibliotecaResource::class,
                PrestamoResource::class,
            ])
            ->discoverResources(in: app_path('Filament/Docentes/Resources'), for: 'App\Filament\Docentes\Resources')
            ->discoverPages(in: app_path('Filament/Docentes/Pages'), for: 'App\Filament\Docentes\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Docentes/Widgets'), for: 'App\Filament\Docentes\Widgets')
            ->widgets([
                /*AccountWidget::class,
                FilamentInfoWidget::class,*/
                DocenteDashboardWidget::class,
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
