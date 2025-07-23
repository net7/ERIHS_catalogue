<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use App\Filament\Resources\CommonProposalResource\Pages\ManageReviewers as Reviewers;


class ManageReviewers extends Reviewers
{
    protected static string $resource = ProposalResource::class;
}
