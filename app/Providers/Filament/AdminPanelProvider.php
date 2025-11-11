<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\CalendarsTabsWidget;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UltimosPrestamosBibliotecaWidget;
use App\Filament\Widgets\UltimosPrestamosInformaticaWidget;
use Filament\Forms\Components\FileUpload;
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
use Jeffgreco13\FilamentBreezy\BreezyCore;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $themeUrl = asset('css/filament/theme.css');
        $calUrl   = asset('css/filament/custom-calendar.css');
        $v1 = @filemtime(public_path('css/filament/theme.css')) ?: time();
        $v2 = @filemtime(public_path('css/filament/custom-calendar.css')) ?: time();

        FilamentAsset::register([
            Css::make('scarso-theme', $themeUrl . '?v=' . $v1),
            /*Css::make('calendar-theme', $calUrl . '?v=' . $v2),*/
        ]);

        return $panel
            ->default()
            ->id('admin')
            ->path('dashboard')
            ->viteTheme('resources/css/filament/custom-sidebar.css')
            ->assets([
                asset('resources/js/filament/custom-sidebar.js')
            ])
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
                UltimosPrestamosInformaticaWidget::class,
                UltimosPrestamosBibliotecaWidget::class,
                CalendarsTabsWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        userMenuLabel: 'My Profile', // Customizes the 'account' link label in the panel User Menu (default = null)
                        shouldRegisterNavigation: false, // Adds a main navigation item for the My Profile page (default = false)
                        navigationGroup: 'Settings', // Sets the navigation group for the My Profile page (default = null)
                        hasAvatars: true, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    )
                    ->avatarUploadComponent(fn($fileUpload) => $fileUpload->disableLabel())

            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
