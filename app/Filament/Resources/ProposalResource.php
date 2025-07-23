<?php

namespace App\Filament\Resources;

use App\Enums\ProposalStatus;
use App\Filament\Resources\ProposalResource\Pages;
use App\Models\Proposal;
use App\Models\User;
use App\Services\UserService;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProposalResource extends CommonProposalResource
{
    protected static ?string $model = Proposal::class;
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $slug = 'proposals';
    protected static ?string $navigationGroup = 'Applications';

    // override the parent class default
    protected static bool $shouldRegisterNavigation = true;
    // global search fields
    protected static ?string $recordTitleAttribute = 'name';


    public static function getNavigationBadge(): ?string
    {
        // return static::getEloquentQuery()->count();
        return static::getModel()::count();
    }

    public static function getRelations(): array
    {
        return [
            // ...
            // AuditsRelationManager::class,
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return ProposalResource::getUrl('general-info', ['record' => $record]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProposals::route('/'),
            'general-info' => Pages\GeneralInfo::route('/{record}/general-info'),
            'application-history' => Pages\ApplicationHistory::route('/{record}/application-history'),
            'rating' => Pages\ProposalRating::route('/{record}/rating'),
            'evaluation' => Pages\ProposalEvaluation::route('{record}/evaluation'),
            'check-evaluation' => Pages\CheckProposalEvaluations::route('{record}/check-evaluation'),
            'manage-reviewers' => Pages\ManageReviewers::route('{record}/manage-reviewers'),
            'conflict-of-interests' => Pages\ConflictOfInterests::route('{record}/conflict-of-interests'),
            'edit-proposal' => Pages\EditProposal::route('{record}/edit-proposal'),
            'update-files' => Pages\ProposalFiles::route('{record}/update-files'),
            'post-access-report' => Pages\PostAccessReport::route('/{record}/post-access-reports'),
        ];
    }

    public static function navigationItems(Proposal $record): array
    {

        $parentItems = parent::navigationItems($record);
        $user = Auth::user();
        $localItems = [];

        if (UserService::canUpdateFiles($user, $record)) {
            $localItems[] = PageNavigationItem::make(__('Manage files'))
                ->url(function () use ($record) {
                    return static::getUrl('update-files', ['record' => $record->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.proposals.proposal-files';
                });
        }

        if (UserService::canUserEditProposal($user, $record)) {
            $localItems[] = PageNavigationItem::make('Edit proposal')
                ->url(function () use ($record) {
                    return static::getUrl('edit-proposal', ['record' => $record->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.proposals.edit-proposal';
                });
        }

        if (UserService::isUserReviewerOfProposal($user, $record) && UserService::canUserEvaluateProposal($user, $record, true)) {
            $localItems[] = PageNavigationItem::make('Conflict of interests')
                ->url(function () use ($record) {
                    return static::getUrl('conflict-of-interests', ['record' => $record->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.proposals.conflict-of-interests';
                });
        }

        if ($user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE])) {
            $localItems[] = PageNavigationItem::make('Manage reviewers')
                ->url(function () use ($record) {
                    return static::getUrl('manage-reviewers', ['record' => $record->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.proposals.manage_reviewers';
                });
        }

        if (UserService::canUserEvaluateProposal($user, $record)) {
            $localItems[] = PageNavigationItem::make('Evaluation')
                ->url(function () use ($record) {
                    return static::getUrl('evaluation', ['record' => $record->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.proposals.evaluation';
                });
        }

        // TODO add check sullo stato in cui sono
        if (UserService::canUserCloseProposalEvaluations($user)) {
            $localItems[] = PageNavigationItem::make('Check Evaluations')
                ->url(function () use ($record) {
                    return static::getUrl('check-evaluation', ['record' => $record->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.proposals.check_evaluations';
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
