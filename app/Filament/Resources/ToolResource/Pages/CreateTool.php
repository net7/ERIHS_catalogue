<?php

namespace App\Filament\Resources\ToolResource\Pages;

use App\Filament\Resources\ToolResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTool extends CreateRecord
{
    protected static string $resource = ToolResource::class;

    protected function getRedirectUrl(): string
    {
        return  $this->getResource()::getUrl('index');
    }
}
