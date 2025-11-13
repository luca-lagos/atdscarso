<?php

namespace App\Providers\Filament;

use App\Filament\Alumnos\Widgets\AlumnoDashboardWidget;
use App\Filament\Resources\PrestamoBibliotecas\PrestamoBibliotecaResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class AlumnosPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('alumnos')
            ->path('alumnos')
            ->login()
            ->profile()
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Biblioteca')
                    ->icon('heroicon-o-building-library'),
                NavigationGroup::make('Informática')
                    ->icon('heroicon-o-computer-desktop'),
                NavigationGroup::make('Gestión de usuarios')
                    ->icon('heroicon-o-user-group'),
            ])
            ->brandName('ATDScarso - Alumnos')
            ->colors([
                'primary' => Color::hex('#6fc635'), // verde claro alumnos
                'success' => Color::hex('#34d399'),
                'warning' => Color::hex('#d97706'),
                'danger'  => Color::hex('#ef4444'),
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
            ->plugins([
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        userMenuLabel: 'Mi perfil', // Customizes the 'account' link label in the panel User Menu (default = null)
                        shouldRegisterNavigation: false, // Adds a main navigation item for the My Profile page (default = false)
                        navigationGroup: 'Settings', // Sets the navigation group for the My Profile page (default = null)
                        hasAvatars: true, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    )
                    ->avatarUploadComponent(fn($fileUpload) => $fileUpload->disableLabel())
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_START,
                function () {
                    // inyecta un atributo data-panel en el body
                    // Nota: devolvemos un pequeño script que lo coloca al cargarse
                    return <<<HTML
                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                      try {
                        var el = document.documentElement || document.body;
                        el.setAttribute('data-panel', 'alumnos');
                      } catch(e) {}
                    });
                    </script>
                    HTML;
                }
            );
    }
}
