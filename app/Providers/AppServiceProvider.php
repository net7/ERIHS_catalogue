<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Policies\ResourceLockPolicy;
use App\Providers\Filament\AppPanelProvider;
use App\Services\ERIHSMailService;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Support\Facades\FilamentColor;
use Filament\Forms;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use pxlrbt\FilamentExcel\Exports\Formatters\ArrayFormatter;


class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ERIHSMailService::class, function (Application  $app) {
            return new ERIHSMailService();
        });

        $this->app->bind(ArrayFormatter::class, function () {
            return new ArrayFormatter("\r\n");
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        Field::macro("tooltip", function(string $tooltip) {
            return $this->hintAction(
                Action::make('help')
                    ->icon('heroicon-o-question-mark-circle')
                    ->extraAttributes(["class" => "text-gray-500"])
                    ->label("")
                    ->tooltip($tooltip)
            );
        });

        Filament::serving(function () {
            // Using Vite
            Filament::registerViteTheme('resources/css/filament/admin/theme.css');
        });

        // trim all text inputs
        Forms\Components\TextInput::configureUsing(function (Forms\Components\TextInput $textInput): void {
            $textInput
                ->dehydrateStateUsing(function (?string $state): ?string {
                    return is_string($state) ? trim($state) : $state;
                });
        });

        \Filament\Resources\Pages\CreateRecord::disableCreateAnother();

        // // loader
        // Filament::registerScripts([
        //     'https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js',
        // ], shouldBeLoadedBeforeCoreScripts: true);

        // // ??
        // Filament::serving(function () {
        //     Filament::registerUserMenuItems([
        //         'account' => UserMenuItem::make()->url(route('filament.pages.profile')),
        //     ]);
        // });

        FilamentColor::register(AppPanelProvider::themeColors());
    }
}
