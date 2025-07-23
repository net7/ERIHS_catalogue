<?php

namespace App\Filament\Resources\CallResource\Pages;

use App\Filament\Resources\CallResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCall extends CreateRecord
{
    protected static string $resource = CallResource::class;

    protected function getRedirectUrl(): string
    {
        return  $this->getResource()::getUrl('index');
    }

}
