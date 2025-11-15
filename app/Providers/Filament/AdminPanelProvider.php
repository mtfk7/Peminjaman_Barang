<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Notifications\Notification;
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
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            
            ->login(\App\Filament\Pages\Auth\Login::class)

            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\Pages\LaporanBarangMasuk::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            // EXPLICITLY define middleware dengan AdminSessionCookie untuk memisahkan session
            // CATATAN: SmartStartSession di web middleware group sudah start session dengan admin_session
            // untuk admin routes, tapi Filament middleware berjalan SEBELUM web middleware group,
            // jadi kita perlu StartSession di sini juga untuk memastikan session tersedia
            // Urutan middleware sangat penting:
            // 1. EncryptCookies - encrypt cookies
            // 2. AddQueuedCookiesToResponse - add cookies to response
            // 3. AdminSessionCookie - start session dengan cookie admin_session
            // 4. AuthenticateSession - validate session (harus setelah StartSession)
            // 5. ShareErrorsFromSession - share errors
            // 6. VerifyCsrfToken - verify CSRF token
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                \App\Http\Middleware\AdminSessionCookie::class, // Start session dengan admin_session
                \App\Http\Middleware\LogAuthenticateSession::class, // Log AuthenticateSession untuk debugging
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\LogAdminRedirects::class, // Log redirects untuk debugging
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            
;
           
    }

    
}
