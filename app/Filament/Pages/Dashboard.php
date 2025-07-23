<?php

namespace App\Filament\Pages;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BasePage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BasePage
{

    public function getColumns(): int | array
    {
        return [
            'md' => 3,
            'xl' => 4,
        ];
    }

    public function getWidgets(): array
    {

        return [
            \Phpsa\FilamentAuthentication\Widgets\LatestUsersWidget::class,
        ];
    }

    public function render(): View
    {

        $user = Auth::user();
        if (!$user->complete_profile) {

            $body = 'Your profile is uncomplete, please fill all the requested field';
            // if (session()->has('was_creating_proposal')){
            //     $body = 'Please complete your profile in order to create new proposals.<br/> Please fill all the requested field';
            // }


            Notification::make()
                ->title('Profile uncomplete!')
                ->body($body)
                ->actions([
                    Action::make('Complete profile')
                        ->button()
                        ->url('/dashboard/profile', shouldOpenInNewTab: false)
                ])
                ->send();
        }


        return parent::render();
    }
}
