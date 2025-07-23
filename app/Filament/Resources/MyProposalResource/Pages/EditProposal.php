<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Filament\Resources\MyProposalResource;

use App\Filament\Resources\CommonProposalResource\Pages\EditProposal as Proposal;

class EditProposal extends Proposal
{
    protected static string $resource = MyProposalResource::class;
}
