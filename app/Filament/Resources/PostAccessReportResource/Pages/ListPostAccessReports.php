<?php

namespace App\Filament\Resources\PostAccessReportResource\Pages;

use App\Filament\Resources\PostAccessReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPostAccessReports extends ListRecords
{
    protected static string $resource = PostAccessReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
