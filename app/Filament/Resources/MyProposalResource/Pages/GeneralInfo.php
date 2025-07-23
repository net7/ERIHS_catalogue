<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Filament\Resources\MyProposalResource;
use App\Filament\Resources\CommonProposalResource\Pages\GeneralInfo as BaseGeneralInfo;

class GeneralInfo extends BaseGeneralInfo
{
    protected static string $resource = MyProposalResource::class;
}
