<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Login;
use App\Models\User;
use App\Settings\KaidoSetting;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\Forms\Components\FileUpload;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Rupadana\ApiService\ApiServicePlugin;

use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Schema;

class AdminPanelProvider extends PanelProvider
{
    private ?KaidoSetting $settings = null;
    //constructor
    public function __construct()
    {
        //this is feels bad but this is the solution that i can think for now :D
        // Check if settings table exists first
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')
                && \Illuminate\Support\Facades\DB::table('settings')->count() > 0) {
                $this->settings = app(KaidoSetting::class);
            }
        } catch (\Throwable $e) {
            $this->settings = null;
        }
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->when($this->settings->login_enabled ?? true, fn($panel) => $panel->login(Login::class))
            ->when($this->settings->registration_enabled ?? false, fn($panel) => $panel->registration())
            ->when($this->settings->password_reset_enabled ?? true, fn($panel) => $panel->passwordReset())
            ->emailVerification()
            ->brandLogo(fn () => new \Illuminate\Support\HtmlString('
                <div class="flex items-center gap-2">
                    <img src="' . asset('images/PCA_Logo.png') . '" class="h-8 w-auto shrink-0" alt="PCA Logo" />
                    <span class="pca-logo-text">PCA Hybridization Portal</span>
                </div>
            '))
            ->colors([
                'primary' => Color::hex('#0b9e4f'), // PCA Green
            ])
            ->font('Inter')
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                function (): string {
                    $html = '<style>
    /* --- Global Primary Color Override (PCA Green) --- */
    :root {
        --primary-50: 236, 253, 245 !important;
        --primary-100: 209, 250, 229 !important;
        --primary-200: 167, 243, 208 !important;
        --primary-300: 110, 231, 183 !important;
        --primary-400: 52, 211, 153 !important;
        --primary-500: 16, 185, 129 !important;
        --primary-600: 11, 158, 79 !important; /* #0b9e4f */
        --primary-700: 4, 120, 87 !important;
        --primary-800: 6, 95, 70 !important;
        --primary-900: 6, 78, 59 !important;
        --primary-950: 2, 44, 34 !important;
    }
    
    /* Sidebar Base */
    .fi-sidebar { background-color: #0b9e4f !important; border-right: none !important; }
    .fi-sidebar-header { background-color: #0b9e4f !important; border-bottom: 1px solid rgba(255,255,255,0.1) !important; container-type: inline-size; }
    .fi-sidebar .fi-sidebar-item-label, .fi-sidebar .fi-sidebar-item-icon { color: #ffffff !important; }
    .fi-sidebar .fi-sidebar-item-button:hover { background-color: #d4e122 !important; }
    .fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-label, .fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-icon { color: #0b9e4f !important; }
    .fi-sidebar .fi-sidebar-item-active > .fi-sidebar-item-button { background-color: #d4e122 !important; box-shadow: 0 4px 12px rgba(212, 225, 34, 0.3) !important; }
    .fi-sidebar .fi-sidebar-item-active > .fi-sidebar-item-button .fi-sidebar-item-label, .fi-sidebar .fi-sidebar-item-active > .fi-sidebar-item-button .fi-sidebar-item-icon { color: #0b9e4f !important; }
    .fi-sidebar .fi-sidebar-group-label { color: rgba(255, 255, 255, 0.9) !important; font-weight: 600 !important; }
    
    /* Logo Visibility Fixes & Responsive Collapse */
    .fi-logo { font-weight: 700 !important; font-size: 1.25rem !important; }
    .fi-sidebar .fi-logo { color: #ffffff !important; }
    .fi-topbar .fi-logo { color: #0b9e4f !important; }
    
    /* Make the collapse < arrow yellow! */
    .fi-sidebar-collapse-button svg,
    .fi-topbar-open-sidebar-button svg { 
        color: #d4e122 !important; 
        fill: #d4e122 !important;
    }
    
    @container (max-width: 150px) {
        /* Hide the real logo link entirely so it does not conflict or navigate away */
        .fi-logo { display: none !important; }
        
        /* Transform the native expand button into the PCA Logo */
        .fi-sidebar-collapse-button { 
            display: block !important; 
            width: 2.5rem !important; 
            height: 2.5rem !important; 
            margin: 0 auto !important;
            background-image: url("/images/PCA_Logo.png") !important;
            background-size: contain !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            background-color: transparent !important;
            border: none !important;
        }
        
        /* Hide the native SVG ">" arrow inside the button */
        .fi-sidebar-collapse-button svg { display: none !important; }
    }

    /* Dashboard Stat Cards (Django-inspired Gradients) */
    .stat-gradient-1 { background: linear-gradient(135deg, #f3e5f5, #e1bee7) !important; border: none !important; box-shadow: 0 4px 12px rgba(106, 27, 154, 0.1) !important; }
    .stat-gradient-2 { background: linear-gradient(135deg, #e8f5e9, #c8e6c9) !important; border: none !important; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.1) !important; }
    .stat-gradient-3 { background: linear-gradient(135deg, #e3f2fd, #bbdefb) !important; border: none !important; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1) !important; }
    .stat-gradient-4 { background: linear-gradient(135deg, #fff7ed, #fed7aa) !important; border: none !important; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.1) !important; }
    .stat-gradient-5 { background: linear-gradient(135deg, #fff8e1, #ffecb3) !important; border: none !important; box-shadow: 0 4px 12px rgba(245, 127, 23, 0.1) !important; }

    /* Table Headers */
    .fi-ta-header-cell { background-color: #0b9e4f !important; border-bottom: 2px solid #098a44 !important; }
    .fi-ta-header-cell-button .fi-ta-header-cell-label, .fi-ta-header-cell>span { color: #ffffff !important; text-transform: uppercase !important; font-size: 0.75rem !important; letter-spacing: 0.05em !important; font-weight: 600 !important; }
    .fi-ta-header-cell .fi-ta-header-cell-sort-icon { color: rgba(255,255,255,0.8) !important; }


    /* --- Dark Mode Styles --- */
    /* Sidebar */
    .dark .fi-sidebar { background-color: #022c22 !important; border-right: 1px solid rgba(255,255,255,0.05) !important; }
    .dark .fi-sidebar-header { background-color: #022c22 !important; border-bottom: 1px solid rgba(255,255,255,0.05) !important; }
    .dark .fi-sidebar .fi-sidebar-item-button:hover { background-color: #064e3b !important; }
    .dark .fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-label, .dark .fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-icon { color: #ffffff !important; }
    .dark .fi-sidebar .fi-sidebar-item-active > .fi-sidebar-item-button { background-color: #059669 !important; box-shadow: 0 4px 12px rgba(0,0,0, 0.3) !important; }
    .dark .fi-sidebar .fi-sidebar-item-active > .fi-sidebar-item-button .fi-sidebar-item-label, .dark .fi-sidebar .fi-sidebar-item-active > .fi-sidebar-item-button .fi-sidebar-item-icon { color: #ffffff !important; }
    
    /* Logo Visibility Dark Mode */
    .dark .fi-logo { color: #ffffff !important; }
    
    /* Dashboard Stat Cards (Dark Mode tinted) */
    .dark .stat-gradient-1 { background: linear-gradient(135deg, rgba(106,27,154,0.4), rgba(69,18,100,0.4)) !important; border: 1px solid rgba(255,255,255,0.05) !important; }
    .dark .stat-gradient-2 { background: linear-gradient(135deg, rgba(6,78,59,0.4), rgba(2,44,34,0.4)) !important; border: 1px solid rgba(255,255,255,0.05) !important; }
    .dark .stat-gradient-3 { background: linear-gradient(135deg, rgba(30,58,138,0.4), rgba(23,37,84,0.4)) !important; border: 1px solid rgba(255,255,255,0.05) !important; }
    .dark .stat-gradient-4 { background: linear-gradient(135deg, rgba(146,64,14,0.4), rgba(69,26,3,0.4)) !important; border: 1px solid rgba(255,255,255,0.05) !important; }
    .dark .stat-gradient-5 { background: linear-gradient(135deg, rgba(127,29,29,0.4), rgba(69,10,10,0.4)) !important; border: 1px solid rgba(255,255,255,0.05) !important; }

    /* Table Headers */
    .dark .fi-ta-header-cell { background-color: #064e3b !important; border-bottom: 2px solid #022c22 !important; }
    .dark .fi-ta-header-cell-button .fi-ta-header-cell-label, .dark .fi-ta-header-cell>span { color: #e5e7eb !important; }
    .dark .fi-ta-header-cell .fi-ta-header-cell-sort-icon { color: rgba(255,255,255,0.6) !important; }

    /* Colored Card Outlines for Sections */
    .fi-section {
        border: 2px solid rgb(var(--primary-600)) !important;
        box-shadow: 0 4px 12px rgba(var(--primary-600), 0.1) !important;
        border-radius: 0.75rem !important;
    }
    .dark .fi-section {
        border-color: rgb(var(--primary-900)) !important;
        box-shadow: 0 4px 12px rgba(0,0,0, 0.4) !important;
    }
    
    /* Subtle tinted section headers */
    .fi-section-header {
        background-color: rgba(var(--primary-600), 0.05) !important;
        border-bottom: 1px solid rgba(var(--primary-600), 0.15) !important;
    }
    .dark .fi-section-header {
        background-color: rgba(var(--primary-900), 0.1) !important;
        border-bottom: 1px solid rgba(var(--primary-900), 0.2) !important;
    }
</style>';

                    return $html;
                }
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Custom widgets are auto-discovered
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
            ->sidebarCollapsibleOnDesktop(true)
            ->authMiddleware([
                Authenticate::class,
            ])
            ->middleware([
                SetTheme::class
            ])
            ->plugins(
                $this->getPlugins()
            )
            ->databaseNotifications();
    }

    private function getPlugins(): array
    {
        $plugins = [
            ThemesPlugin::make(),
            FilamentShieldPlugin::make(),
            ApiServicePlugin::make(),
            BreezyCore::make()
                ->myProfile(
                    shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                    shouldRegisterNavigation: true, // Adds a main navigation item for the My Profile page (default = false)
                    navigationGroup: 'Settings', // Sets the navigation group for the My Profile page (default = null)
                    hasAvatars: true, // Enables the avatar upload form component (default = false)
                    slug: 'my-profile'
                )
                ->customMyProfilePage(\App\Filament\Pages\MyProfile::class)
                ->withoutMyProfileComponents([
                    'personal_info',
                    'update_password',
                ])
                ->avatarUploadComponent(fn($fileUpload) => $fileUpload->disableLabel())
                // OR, replace with your own component
                ->avatarUploadComponent(
                    fn() => FileUpload::make('avatar_url')
                        ->image()
                        ->disk('public')
                )
                ->enableTwoFactorAuthentication(),
        ];

        if ($this->settings->sso_enabled ?? true) {
            $plugins[] =
                FilamentSocialitePlugin::make()
                ->providers([
                    Provider::make('google')
                        ->label('Google')
                        ->icon('fab-google')
                        ->color(Color::hex('#2f2a6b'))
                        ->outlined(true)
                        ->stateless(false)
                ])->registration(true)
                ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
                    $user = User::where('email', $oauthUser->getEmail())->first();

                    if (!$user) {
                        // If user doesn't exist, we don't allow auto-registration for PCA roles.
                        // They must be created by an Admin first.
                        throw new \Exception('Your email is not registered in the PCA Hybrid System. Please contact your administrator.');
                    }

                    // Update existing user with latest info if needed
                    $user->name = $oauthUser->getName();
                    $user->email_verified_at = $user->email_verified_at ?? now();
                    $user->save();

                    return $user;
                });
        }
        return $plugins;
    }
}
