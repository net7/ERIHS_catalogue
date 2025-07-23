<?php

namespace App\Filament\Resources\CallResource\Pages;

use App\Filament\Resources\CallResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCall extends EditRecord
{
    protected static string $resource = CallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return  $this->getResource()::getUrl('index');
    }
}
