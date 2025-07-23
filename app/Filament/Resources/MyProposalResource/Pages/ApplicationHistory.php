<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Filament\Resources\MyProposalResource;
use App\Models\Proposal;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\CommonProposalResource\Pages\ApplicationHistory as BaseApplicationHistory;

class ApplicationHistory extends BaseApplicationHistory
{
    protected static string $resource = MyProposalResource::class;

    // protected static string $view = 'filament.resources.proposal-resource.pages.application-history';


}
