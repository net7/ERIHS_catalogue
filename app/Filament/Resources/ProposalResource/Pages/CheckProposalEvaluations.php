<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use App\Filament\Resources\CommonProposalResource\Pages\CheckProposalEvaluations as CheckEvaluation;


class CheckProposalEvaluations extends CheckEvaluation
{
    protected static string $resource = ProposalResource::class;
}
