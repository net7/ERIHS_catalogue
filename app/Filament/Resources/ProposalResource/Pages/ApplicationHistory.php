<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use App\Models\Proposal;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\CommonProposalResource\Pages\ApplicationHistory as BaseApplicationHistory;

class ApplicationHistory extends BaseApplicationHistory
{
    protected static string $resource = ProposalResource::class;

    // protected static string $view = 'filament.resources.proposal-resource.pages.application-history';

}
