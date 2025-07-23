<?php

namespace App\Filament\Resources\ProposalReviewerResource\Pages;

use App\Filament\Resources\ProposalReviewerResource;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProposalReviewers extends ListRecords
{
    protected static string $resource = ProposalReviewerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
