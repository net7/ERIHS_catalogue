<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

use Phpsa\FilamentAuthentication\Actions\ImpersonateLink;
use Laravel\Sanctum\PersonalAccessToken;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\DeleteAction::make(),
            Action::make('generateToken')
                ->label('Generate API Token')
                ->action(function () {
                    $record = $this->record;
                    self::deleteOldToken($record);
                    $token = $record->createToken('auth_token')->plainTextToken;
                    $record->api_token = $token;
                    $record->save();
                    Notification::make()
                        ->title('Token creato')
                        ->success()
                        ->send();
                })
                ->visible(fn($livewire) => auth()->user()->hasRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE])),

        ];
        $impersonateAction = $this->impersonateAction();
        if ($impersonateAction != null) {
            $actions []= $impersonateAction;
        }
        return $actions;
    }

    protected static function deleteOldToken($record): void
    {
        PersonalAccessToken::where('tokenable_id', $record->id)
            ->where('tokenable_type', get_class($record))
            ->delete();
    }

    protected function impersonateAction(): ?Action
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $record = $this->getRecord();
        $user = Filament::auth()->user();
        if ($user === null || ImpersonateLink::allowed($user, $record) === false) {
            return null;
        }

        return Action::make('impersonate')
            ->label(__('filament-authentication::filament-authentication.button.impersonate'))
            ->action(fn() => ImpersonateLink::impersonate($record));
    }
}
