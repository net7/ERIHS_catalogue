<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Filament\Resources\MyProposalResource;
use App\Filament\Resources\CommonProposalResource\Pages\CheckProposalEvaluations as CheckEvaluation;


class CheckProposalEvaluations extends CheckEvaluation
{
    protected static string $resource = MyProposalResource::class;
}
