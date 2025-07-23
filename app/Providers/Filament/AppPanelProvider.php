<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Widgets\CallsChart;
use App\Filament\Widgets\CallsOverview;
use App\Filament\Widgets\Help;
use App\Filament\Widgets\ProposalsOverview;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Kenepa\ResourceLock\ResourceLockPlugin;
use Phpsa\FilamentAuthentication\FilamentAuthentication;
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\VerticalAlignment;

class AppPanelProvider extends PanelProvider
{

    public static function themeColors()
    {
        return [
            'danger' => [
                50 => '#fff1f2',
                100 => '#FDD9DF',
                200 => '#FBB2BF',
                300 => '#F88C9E',
                400 => '#F88C9E',
                500 => '#F43F5E',
                600 => '#E11C48',
                700 => '#B4173A',
                800 => '#621926',
                900 => '#310D13',
                950 => '#310D13',
            ],
            'primary' => [

                50 => '#D7EEF0',
                100 => '#C3E6E9',
                200 => '#B0DEE2',
                300 => '#9CD5DA',
                400 => '#44676A',
                500 => '#395658',
                600 => '#2D4547',
                700 => '#223435',
                800 => '#172223',
                900 => '#0E1515',
                950 => '#0E1515',
            ],
            'success' => [
                50 => '#F2FDF5',
                100 => '#D3F3DF',
                200 => '#A7E8BF',
                300 => '#7ADC9E',
                400 => '#4ED17E',
                500 => '#22C55E',
                600 => '#16A34A',
                700 => '#147638',
                800 => '#0E4F26',
                900 => '#072713',
                950 => '#072713',
            ],
            'warning' => [
                50 => '#FFFBEB',
                100 => '#FDECCE',
                200 => '#FBD89D',
                300 => '#F9C56D',
                400 => '#F7B13C',
                500 => '#F59E0B',
                600 => '#D97706',
                700 => '#A96E09',
                800 => '#623F04',
                900 => '#312002',
                950 => '#312002',
            ],
            'gray' => Color::Gray,
            'info' => Color::Blue,
            'custom' => [

                50 => '#D7EEF0',
                100 => '#C3E6E9',
                200 => '#B0DEE2',
                300 => '#9CD5DA',
                400 => '#44676A',
                500 => '#395658',
                600 => '#2D4547',
                700 => '#223435',
                800 => '#172223',
                900 => '#0E1515',
                950 => '#0E1515',
            ],

        ];
    }


    public function panel(Panel $panel): Panel
    {

        // Notifications::alignment(Alignment::Center);
        Notifications::verticalAlignment(VerticalAlignment::End);


        $menuItems = [
            MenuItem::make()
                ->label('Dashboard')
                // ->label(fn() :?string => Route::currentRouteName())
                ->icon('heroicon-o-squares-plus')
                ->url(fn(): ?string => route('dashboard'))
                // ->hidden(fn() :bool => strpos(Route::currentRouteName(), 'dashboard') !== false)
                ->hidden(fn(): bool => strpos(Route::current()->uri(), 'dashboard') !== false),
            MenuItem::make()
                ->label('Catalogue')
                ->icon('heroicon-o-newspaper')
                ->url(fn(): ?string => route('catalogue'))
                ->hidden(fn(): bool => strpos(Route::currentRouteName(), 'catalogue') !== false),
            MenuItem::make()
                ->label('Write proposal')
                ->icon('heroicon-o-clipboard-document-check')
                ->url(fn(): ?string => route('cart'))
                ->hidden(fn(): bool => strpos(Route::currentRouteName(), 'cart') !== false),

            MenuItem::make()
                ->label('Favourites')
                ->icon('heroicon-o-bookmark')
                ->url(fn(): ?string => route('favourites'))
                ->hidden(fn(): bool => strpos(Route::currentRouteName(), 'favourite') !== false),

            'logout' => MenuItem::make()->label('Logout')
        ];

        return $panel
            ->default()
            ->id('app')
            ->path('/dashboard')
            ->profile(EditProfile::class)
            ->colors($this->themeColors())
            ->font('Montserrat', provider: GoogleFontProvider::class)

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                Help::class,
                ProposalsOverview::class,
                CallsOverview::class,
                CallsChart::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->favicon(asset('images/favicon.png'))
            ->darkMode(false)
            // ->databaseNotifications()
            // ->databaseNotificationsPolling('5s')

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
            ->brandName(config('app.name'))
            ->brandLogo(asset('images/erihs_logo.png'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationItems([
                NavigationItem::make('Catalogue')
                    ->url(fn(): string => route('catalogue'))
                    ->icon('heroicon-o-newspaper')
            ])
            ->sidebarCollapsibleOnDesktop()
            // ->sidebarFullyCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('My documents')
                ,
                NavigationGroup::make()
                    ->label('Applications'),
                NavigationGroup::make()
                    ->label('Authentication')
                ,
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog')
                    ->collapsed(),
            ])
            ->plugin(ResourceLockPlugin::make())
            ->plugin(FilamentAuthentication::make())
            ->userMenuItems($menuItems)
        ;
    }
}
