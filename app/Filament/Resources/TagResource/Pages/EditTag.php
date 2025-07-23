<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use App\Forms\Components\ActionDisabledTooltip;
use App\Services\TagsService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\Concerns\HasTooltip;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditTag extends EditRecord
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        $tag = $this->getRecord();
        return [

            Actions\DeleteAction::make()
                ->hidden(fn ($record) => TagsService::isUsed($record) || $record->external_id),
            Action::make('cannot-delete-tooltip')
            ->disabled()
            ->label(new HtmlString( 'You cannot delete this record <br/>as it is in use or is a foundational one'))
                ->visible(fn ($record) => TagsService::isUsed($record) || $record->external_id),
        ];
    }
}
