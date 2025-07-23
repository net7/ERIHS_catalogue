<?php

namespace App\Filament\Resources\ProposalReviewerResource\Pages;

use App\Filament\Resources\ProposalReviewerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProposalReviewer extends EditRecord
{
    protected static string $resource = ProposalReviewerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
