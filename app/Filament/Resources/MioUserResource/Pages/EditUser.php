<?php

namespace App\Filament\Resources\MioUserResource\Pages;

use App\Filament\Resources\MioUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = MioUserResource::class;
    protected static ?string $breadcrumb = "User Roles";
    protected static ?string $title = "Manage user roles";

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
    
    protected function getFormActions(): array
   
    {
        return [];
    }
}
