<?php

namespace App\Filament\Resources\CommonProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use App\Models\Proposal;
use App\Models\ProposalService;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ApplicationHistory extends Page
{
    use HasPageSidebar;
    use InteractsWithRecord;

    public ProposalResource $proposalResource;
    protected static string $resource = ProposalResource::class;

    protected static ?string $breadcrumb = "Application history";
    protected static ?string $title = "Application history";



    protected static string $view = 'filament.resources.proposal-resource.pages.application-history';

    public function getModel(): string
    {
        return static::getResource()::getModel();
    }



    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public static function sidebar(Proposal $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->setNavigationItems(ProposalResource::navigationItems($record));
    }
}
