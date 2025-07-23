<?php

namespace App\Filament\Resources;

use App\Enums\ProposalStatus;
use App\Filament\Resources\MyProposalResource\Pages;
use App\Models\MyProposal;
use App\Models\Proposal;
use Filament\Tables\Table;
use App\Services\ProposalService;
use App\Services\UserService;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MyProposalResource extends ProposalResource
{
    protected static ?string $pluralModelLabel = 'My Applications';

    protected static ?string $modelLabel = 'Application';

    protected static ?string $navigationLabel = 'List of applications';

    protected static ?string $navigationGroup = 'My documents';
    protected static ?string $model = MyProposal::class;

    protected static ?string $slug = 'my-proposals';

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $activeNavigationIcon = 'heroicon-o-document-text';

    // override the parent class default
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }
    // global search fields

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return MyProposalResource::getUrl('general-info', ['record' => $record]);
    }


    public static function table(Table $table): Table
    {
        $table = parent::table($table);
        $table->emptyStateHeading('No Applications');
        return $table;
    }

    public static function getEloquentQuery(): Builder
    {
        return ProposalService::getMySubmittedProposalQuery();
    }

    public static function getPages(): array
    {
        $pages = parent::getPages();

        return array_merge($pages, [
            'index' => Pages\ListMyProposals::route('/'),
            'general-info' => Pages\GeneralInfo::route('/{record}/general-info'),
            'application-history' => Pages\ApplicationHistory::route('/{record}/application-history'),
            'edit-proposal' => Pages\EditProposal::route('/{record}/edit-proposal'),
            'service-feasibility' => Pages\ServiceFeasibility::route('/{record}/service-feasibility'),
            'check-evaluation' => Pages\CheckProposalEvaluations::route('{record}/check-evaluation'),
            'manage-reviewers' => Pages\ManageReviewers::route('{record}/manage-reviewers'),
            'service-access' => Pages\ServiceAccess::route('/{record}/service-access'),
            'post-access-report' => Pages\PostAccessReport::route('/{record}/post-access-reports'),
            'update-files' => Pages\ProposalFiles::route('{record}/update-files')

        ]);
    }
    public static function canCreate(): bool
    {
        return false;
    }

    public static function navigationItems(Proposal $record): array
    {

        $parentItems = parent::navigationItems($record);

        $localItems = [];

        if (UserService::canUserEvaluateFeasability(Auth::user(), $record)) {
            $localItems[] = PageNavigationItem::make('Check Feasibility')
                ->url(function () use ($record) {
                    return static::getUrl('service-feasibility', ['record' => $record->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.my_proposals.service_feasibility';
                });
        }

        if (UserService::canUserScheduleAccess(Auth::user(), $record)) {
            $localItems[] = PageNavigationItem::make('Service access')
                ->url(function () use ($record) {
                    return static::getUrl('service-access', ['record' => $record->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.my_proposals.service_access';
                });
        }

        if (
            $record->hasBeenInStatus(ProposalStatus::ARCHIVED->value) &&
            UserService::canEditPostAccessReport(Auth::user(), $record)
        ) {
            $localItems[] = PageNavigationItem::make('Handle post access duties')
                ->url(function () use ($record) {
                    return static::getUrl('post-access-report', ['record' => $record->postDutiesReport->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.app.resources.my-proposals.post-access-report';
                });
        }



        return array_merge($parentItems, $localItems);
    }
}
