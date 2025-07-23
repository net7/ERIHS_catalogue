<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Filament\Resources\MyProposalResource;
use App\Filament\Resources\CommonProposalResource\Pages\ManageReviewers as Reviewers;


class ManageReviewers extends Reviewers
{
    protected static string $resource = MyProposalResource::class;
}
