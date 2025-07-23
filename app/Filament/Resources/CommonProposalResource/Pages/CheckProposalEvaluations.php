<?php

namespace App\Filament\Resources\CommonProposalResource\Pages;

use App\Enums\ProposalStatus;
use App\Filament\Resources\ProposalResource;
use App\Models\Proposal;
use App\Models\ProposalEvaluation;
use App\Models\User;
use App\Services\ProposalService;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use IbrahimBougaoua\FilamentRatingStar\Columns\Components\RatingStar;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use League\CommonMark\Extension\Table\TableSection;

class CheckProposalEvaluations extends Page implements HasForms, HasTable
{

    use HasPageSidebar;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithRecord;


    protected static string $view = 'filament.resources.proposal-resource.pages.list-proposal-evaluations';

    protected static ?string $breadcrumb = "Check Evaluation";
    protected static ?string $title = "Check Evaluation";

    public function table(Table $table): Table
    {
        $record = $this->record;
        $canBeRanked = $record->canBeRanked();
        $user = Auth::user();


        return $table
            ->relationship(fn(): HasMany => $record->evaluations())
            ->inverseRelationship('proposal')
            ->heading('Reviews')
            ->columns([
                Grid::make([
                    'sm' => 2,
                ])->schema([
                    Stack::make([
                        TextColumn::make('reviewer_label ')
                            ->weight('bold')
                            ->columnSpanFull()
                            ->state('Reviewer'),
                        TextColumn::make('reviewer')
                            ->state(function (ProposalEvaluation $pe): string {
                                return $pe->reviewer->getFilamentName() . ' - ' . $pe->reviewer->email;
                            })
                            ->label(__('Reviewer')),
                    ]),
                    Stack::make([
                        Grid::make([
                            'sm' => 2,
                        ])->schema([

                            TextColumn::make('excellence_block_label')
                                ->grow(true)
                                ->weight('bold')
                                ->state(__('Excellence'))
                                ->columnSpanFull(),

                            TextColumn::make('excellence_relevance_label')
                                ->grow(false)
                                ->state(__('Relevance of the research questions')),
                            RatingStar::make('excellence_relevance'),

                            TextColumn::make('excellence_methodology_label')
                                ->grow(false)
                                ->state(__('Methodology and research plan')),
                            RatingStar::make('excellence_methodology'),

                            TextColumn::make('excellence_originality_label')
                                ->grow(false)
                                ->state(__('Originality')),
                            RatingStar::make('excellence_originality'),

                            TextColumn::make('excellence_expertise_label')
                                ->grow(false)
                                ->state(__('Expertise of user group')),
                            RatingStar::make('excellence_expertise'),

                            TextColumn::make('excellence_timeliness_label')
                                ->grow(false)
                                ->state(__('Timeliness')),
                            RatingStar::make('excellence_timeliness'),

                            TextColumn::make('excellence_state_of_the_art_label')
                                ->grow(false)
                                ->state(__('State-of-the-art')),
                            RatingStar::make('excellence_state_of_the_art'),

                            TextColumn::make('impact_block_label')
                                ->grow(true)
                                ->weight('bold')
                                ->state(__('Impact'))
                                ->columnSpanFull(),

                            TextColumn::make('impact_research_label')
                                ->grow(false)
                                ->state(__('Research community impact')),
                            RatingStar::make('impact_research'),

                            TextColumn::make('impact_knowledge_sharing_label')
                                ->grow(false)
                                ->state(__('Knowledge Sharing')),
                            RatingStar::make('impact_knowledge_sharing'),

                            TextColumn::make('impact_innovation_potential_label')
                                ->grow(false)
                                ->state(__('Innovation Potential')),
                            RatingStar::make('impact_innovation_potential'),

                            TextColumn::make('impact_open_access_label')
                                ->grow(false)
                                ->state(__('Open Access')),
                            RatingStar::make('impact_open_access'),

                            TextColumn::make('impact_expected_impacts_label')
                                ->grow(false)
                                ->state(__('Expected Impacts on Society or Industry')),
                            RatingStar::make('impact_expected_impacts'),
                        ]),


                        Grid::make([
                            'sm' => 1,
                        ])->schema([

                            TextColumn::make('individual_weighted_average_label ')
                                ->grow(false)
                                ->weight('bold')
                                ->state('Average'),
                            TextColumn::make('individual_weighted_average')
                                ->label( 'Weighted Average')
                                ->numeric(decimalPlaces: 2)
                                ->state(function (ProposalEvaluation $pe): float {
                                    return ProposalEvaluation::calculateSingleWeightedAverage(
                                        $pe->excellence_relevance,
                                        $pe->excellence_methodology,
                                        $pe->excellence_originality,
                                        $pe->excellence_expertise,
                                        $pe->excellence_timeliness,
                                        $pe->excellence_state_of_the_art,
                                        $pe->impact_research,
                                        $pe->impact_knowledge_sharing,
                                        $pe->impact_innovation_potential,
                                        $pe->impact_open_access,
                                        $pe->impact_expected_impacts
                                    );
                                }),

                            TextColumn::make('comment_label ')
                                ->weight('bold')
                                ->state('Reviewer comment'),
                            TextColumn::make('comment')
                        ]),

                    ])->columnSpanFull(),

                    Stack::make([
                        TextColumn::make('weighted_average')
                            ->label('Weighted Average')
                            ->numeric(decimalPlaces: 2)
                            ->summarize(Summarizer::make()
                                ->hidden(!$user->hasAnyRole([\App\Models\User::HELP_DESK_ROLE, \App\Models\User::ADMIN_ROLE]))
                                ->label('Weighted Average')
                                ->using(fn() => ProposalEvaluation::calculateAverages($this->record->id))
                            ),
                    ])->columnSpanFull(),


                ]),

            ])
            ->headerActions([
                Action::make('Rank proposal')
                    ->label('Rank proposal')
                    ->requiresConfirmation()
                    ->form(
                        fn(Form $form) => $form
                            ->schema([
                                Radio::make('proposal_result')
                                    ->label('Proposal Result')
                                    ->options(self::getOptions($record->status))
                                    ->default(function () use ($record) {
                                        if ($record->status == ProposalStatus::RANKED_MAIN_LIST->value) {
                                            return ProposalStatus::RANKED_MAIN_LIST->value;
                                        }
                                        if ($record->status == ProposalStatus::RANKED_RESERVE_LIST->value) {
                                            return ProposalStatus::RANKED_RESERVE_LIST->value;
                                        }
                                        return null;
                                    })
                                    ->disabled(!$canBeRanked) // Disabilita tutto se $canBeRanked è false
                                    ->required(),

                                Textarea::make('proposal_result_notes')
                                    ->default(ProposalService::getRankNotes($record))
                                    ->label('Ranking notes')
                                    ->disabled(!$canBeRanked),
                            ])
                    )
                    ->action(function (array $data) {

                        try {
                            $this->record->rank(Arr::get($data, 'proposal_result'), Arr::get($data, 'proposal_result_notes'));
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Error while ranking proposal')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            Log::error($e->getMessage());
                            Log::error($e->getTraceAsString());
                        }
                        Notification::make()
                            ->title('Success')
                            ->body('Proposal ranked successfully.') // . json_encode($data))
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Rank proposal')
                    ->disabled(!$canBeRanked)
                    ->visible($canBeRanked),
            ])
            ->paginated(false);
    }


    public function mount(int|string $record): void
    {

        abort_unless(
            auth()->user()->hasAnyRole([\App\Models\User::HELP_DESK_ROLE, \App\Models\User::ADMIN_ROLE]),
            403
        );
        $this->record = $this->resolveRecord($record);
    }


    public static function sidebar(Proposal $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->setNavigationItems(ProposalResource::navigationItems($record));
    }

    public static function getOptions($status)
    {
        $options = [
            ProposalStatus::RANKED_MAIN_LIST->value => 'In main list',
            ProposalStatus::RANKED_RESERVE_LIST->value => 'In reserve list',
            ProposalStatus::RANKED_BELOW_THRESHOLD->value => 'Below threshold',
        ];
        if ($status == ProposalStatus::RANKED_RESERVE_LIST->value) {
            unset($options[ProposalStatus::RANKED_BELOW_THRESHOLD->value]);
        }
        return $options;
    }
}
