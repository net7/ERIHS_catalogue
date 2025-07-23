<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Enums\ProposalStatus;
use App\Enums\ProposalStatusGroups;
use App\Filament\Resources\MyProposalResource;
use App\Models\Proposal;
use App\Services\ProposalService;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListMyProposals extends ListRecords
{

    protected static string $resource = MyProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'all';
    }

    public function getTabs(): array {
        return [
            'Proposals involved in' => Tab::make('Proposals involved in')
                ->badge(ProposalService::getMySubmittedProposalQuery()
                ->join('applicant_proposal', 'proposal_id', '=', 'proposals.id')
                ->where('applicant_proposal.applicant_id', '=' , Auth::user()->id)->count())

                ->modifyQueryUsing(function ($query){
                    return $query
                        ->join('applicant_proposal', 'proposal_id', '=', 'proposals.id')
                        ->where('applicant_proposal.applicant_id', '=' , Auth::user()->id)
                        ->select('proposals.*');
                }),
            'In Review' => Tab::make('In Review')
                ->badge(ProposalService::getMySubmittedProposalQuery()->whereIn('status',
                    ProposalStatus::getStatesByGroup(ProposalStatusGroups::REVIEWABLE->value))->count())
                ->modifyQueryUsing(function ($query){
                    return $query->whereIn('status', ProposalStatus::getStatesByGroup(ProposalStatusGroups::REVIEWABLE->value));
                }),

            'Awaiting Feasibility' => Tab::make('Awaiting Feasibility')
                ->badge(ProposalService::getMySubmittedProposalQuery()
                    ->whereIn('status', ProposalStatus::getStatesByGroup(ProposalStatusGroups::IN_FEASIBILITY->value))->count())

                ->modifyQueryUsing(function ($query){
                    return $query->whereIn('status', ProposalStatus::getStatesByGroup(ProposalStatusGroups::IN_FEASIBILITY->value));
                }),

            'All' => Tab::make('All')

                ->badge(badge:ProposalService::getMySubmittedProposalQuery()->count()),

        ];
    }

    // public function table(Table $table): Table
    // {
    //     $table = parent::table($table);
    //     //Check available options on statuses
    //     $statuses = ProposalService::getMySubmittedProposalQuery()
    //         ->groupBy('status')
    //         ->pluck('status')->toArray();
    //     $statusArray = [];
    //     foreach ($statuses as $status) {
    //         $statusArray[$status] = $status;
    //     }
    //     $table->filters([
    //         Filter::make('filter_by_status')
    //             ->form([Select::make('status')->options($statusArray)])
    //             ->query(function (Builder $query, array $data): Builder {
    //                 return $query->when($data['status'], fn (Builder $query, $status) => $query->whereIn('status',  [$status]));
    //             })
    //     ]);
    //     return $table;
    // }
}
