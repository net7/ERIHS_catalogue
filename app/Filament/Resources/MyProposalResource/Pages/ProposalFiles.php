<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Filament\Resources\MyProposalResource;
use App\Filament\Resources\ProposalResource\Pages\ProposalFiles as BaseProposalFiles;
class ProposalFiles extends BaseProposalFiles
{

    protected static string $resource = MyProposalResource::class;

    // protected static string $view = 'filament.resources.proposal-resource.pages.proposal-files';

}
