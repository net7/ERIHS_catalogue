<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Filament\Resources\MyProposalResource;
use App\Models\Proposal;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMyProposal extends CreateRecord
{
    use HasPageSidebar; 

    // public Proposal $record; 

    protected static string $resource = MyProposalResource::class;
    // protected static ?string $breadcrumb = "Application history";
    // protected static ?string $title = "Application history";
    
}
