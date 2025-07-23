<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\CommonProposalResource\Pages\PostAccessReport as PagesPostAccessReport;
use App\Filament\Resources\PostAccessReportResource;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Illuminate\Support\HtmlString;

class PostAccessReport extends PagesPostAccessReport
{
    use HasPageSidebar;

    protected static string $resource = PostAccessReportResource::class;

    protected static string $view = 'filament.resources.proposal-resource.pages.post-access-reports';

}
