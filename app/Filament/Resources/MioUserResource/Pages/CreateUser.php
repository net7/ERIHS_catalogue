<?php

namespace App\Filament\Resources\MioUserResource\Pages;

use App\Filament\Resources\MioUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = MioUserResource::class;
}
