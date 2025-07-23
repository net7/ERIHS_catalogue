<?php

namespace App\Filament\Resources\PostAccessReportResource\Pages;

use App\Filament\Resources\PostAccessReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPostAccessReport extends EditRecord
{
    protected static string $resource = PostAccessReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
