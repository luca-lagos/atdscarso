<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\CalendarsTabsWidget;
use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Registrar el CSS personalizado del panel
        FilamentAsset::register([
            Css::make('scarso-theme', resource_path('css/filament/theme.css')),
            Css::make('calendar-theme', resource_path('css/filament/custom-calendar.css')), // ya lo tenés
        ]);

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->profile()
            ->login()
            ->brandName('Escuela Scarso')
            // ->darkMode(true) // opcional: forzar dark; si no, respeta prefers-color-scheme
            ->colors([
                'primary' => Color::hex('#7B1E2B'),
                'success' => Color::hex('#2E7D32'),
                'warning' => Color::hex('#B26A00'),
                'danger'  => Color::hex('#C62828'),
            ])
            // Si igual querés inyectar por hook (no necesario con assets, lo dejo comentado)
            // ->renderHook(
            //     PanelsRenderHook::STYLES_AFTER,
            //     fn(): string => ''
            // )
            //->favicon(asset('images/favicon.svg'))
            //->brandLogo(asset('images/logo.svg'))
            //->brandLogoHeight('28px')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //AccountWidget::class,
                // StatsOverview base de Filament es simple; usamos uno custom abajo
                // FilamentInfoWidget::class, // lo escondemos para limpiar el dashboard
                StatsOverview::class,
                //CalendarsTabsWidget::class,
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
        /*->assets([
                FilamentAsset::makeCss('theme', 'resources/css/filament/theme.css')->version(filemtime(base_path('resources/css/filament/theme.css'))),
                FilamentAsset::makeCss('custom-calendar', 'resources/css/filament/custom-calendar.css')->version(filemtime(base_path('resources/css/filament/custom-calendar.css'))),
            ])*/
    }
}
