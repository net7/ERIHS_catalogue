<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Enums\ProposalStatus;
use App\Filament\Resources\MyProposalResource;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ServiceFeasibility extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithRecord;
    use HasPageSidebar;

    protected static string $resource = MyProposalResource::class;

    protected static string $view = 'filament.resources.proposal-resource.pages.list-proposal-services';

    public function table(Table $table): Table
    {
        $record = $this->record;
        $user = Auth::user();
        // $userOrganizationIds = $user->organizations()->pluck('id')->toArray();



        $feasibilityModalText = new HtmlString('
                                    Provide comments where necessary on each criterion accompanied by N/A or a score between 1-5: <br/><br/>
                                    
                                    
                                    Risk to object;<br/>
                                    Implementation plan and timeline;<br/>
                                    Health and Safety Risks.<br/>

                                    <br/>

                                    Indicate use of Helpdesk with Y/N.
                                    ');

        $feasibilityTextareaPlaceholder = new HtmlString('
Risk to object: ...
Implementation plan and timeline: ...
Health and Safety Risks: ...
Indicate use of Helpdesk with Y/N: ...
');

        //Devo mettere l'action solo se lo user corrente ha lo stesso organization_id del service
        return $table
            ->relationship(
                fn(): BelongsToMany => env('IS_TEST', false) ?
                    $record->services() :
                    $record->servicesByServiceManager($user->id)
            )
            ->columns([
                TextColumn::make('title')
                    ->label(__('Service name'))
                    ->limit(25)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
                IconColumn::make('feasible')
                    ->label('Feasibility')
                    ->icons([
                        'heroicon-o-check-circle' => function ($record) {
                            $proposalService = $this->record->proposalServiceOfService($record->service_id);
                            return $proposalService->feasible == \App\Models\ProposalService::FEASIBLE;
                        },
                        'heroicon-o-x-circle' => function ($record) {
                            $proposalService = $this->record->proposalServiceOfService($record->service_id);
                            return $proposalService->feasible == \App\Models\ProposalService::NOT_FEASIBLE;
                        },
                        'heroicon-o-arrow-path' => function ($record) {
                            $proposalService = $this->record->proposalServiceOfService($record->service_id);
                            return $proposalService->feasible == \App\Models\ProposalService::TO_BE_DEFINED;
                        }
                    ])
                    ->colors([
                        'secondary',
                        'danger' => 'not_feasible',
                        'gray' => 'to_be_defined',
                        'success' => 'feasible',
                    ])
                    ->hidden($this->record->status == ProposalStatus::SECOND_DRAFT->value),
                TextColumn::make('motivation')
                    ->label(__('Comment'))
                    ->limit(65535)
                    ->getStateUsing(function ($record) {
                        $proposalService = $this->record->proposalServiceOfService($record->service_id);
                        return $proposalService->motivation;
                    })
            ])
            ->actions([
                Action::make('makeItUnFeasibale')
                    ->visible($this->record->status == ProposalStatus::SUBMITTED->value
                        || $this->record->status == ProposalStatus::RESUBMITTED->value)
                    ->form([
                        TextArea::make('motivation')
                            ->autosize()
                            ->placeholder($feasibilityTextareaPlaceholder)
                            ->rows(10)
                            ->label('Motivation')->required()
                            ->default(function ($record) {
                                $proposalService = $this->record->proposalServiceOfService($record->service_id);
                                return $proposalService->motivation;
                            })
                    ])
                    ->label('Mark as unfeasible')
                    ->action(function (array $data, $record) {
                        $proposalService = $this->record->proposalServiceOfService($record->service_id);
                        $proposalService->feasible = \App\Models\ProposalService::NOT_FEASIBLE;
                        $motivation = $data['motivation'];
                        $proposalService->motivation = $motivation;
                        $proposalService->save();
                        Notification::make()
                            ->title('Success')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Mark as unfeasible')
                    ->modalAlignment('justify')
                    ->modalDescription($feasibilityModalText),
                Action::make('makeItFeasibale')
                    ->label('Mark as feasible')
                    ->visible($this->record->status == ProposalStatus::SUBMITTED->value
                        || $this->record->status == ProposalStatus::RESUBMITTED->value)
                    ->form([
                        TextArea::make('motivation')
                            ->autosize()
                            ->placeholder($feasibilityTextareaPlaceholder)
                            ->rows(10)
                            ->label('Motivation')->required()
                            ->default(function ($record) {
                                $proposalService = $this->record->proposalServiceOfService($record->service_id);
                                return $proposalService->motivation;
                            })
                    ])
                    ->action(function (array $data, $record) {
                        $proposalService = $this->record->proposalServiceOfService($record->service_id);
                        $proposalService->feasible = 'feasible';
                        $motivation = $data['motivation'];
                        $proposalService->motivation = $motivation;
                        $proposalService->save();
                        Notification::make()
                            ->title('Success')
                            ->success()
                            ->send();
                    })->hidden($this->record->status == ProposalStatus::DRAFT->value || $this->record->status == ProposalStatus::SECOND_DRAFT->value)
                    ->requiresConfirmation()
                    ->modalHeading('Mark as feasibile')
                    ->modalDescription($feasibilityModalText)
    ,

            ]);
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}
