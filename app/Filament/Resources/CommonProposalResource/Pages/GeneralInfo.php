<?php

namespace App\Filament\Resources\CommonProposalResource\Pages;

use App\Enums\ProposalReviewerStatus;
use App\Filament\Resources\ProposalResource;
use App\Models\Service;
use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class GeneralInfo extends EditRecord
{
    use HasPageSidebar;

    protected static string $resource = ProposalResource::class;
    protected static ?string $breadcrumb = "General information";
    protected static ?string $title = "General information";

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        return [

            Action::make('edit-draft')
                ->hidden(function ($record) use ($user) {
                    if (!$record->isInDraftState() || !$record->isUserLeader($user)) {
                        return true;
                    }
                })
                ->label(
                    __('Complete proposal')
                )
                ->url('/proposal')
                ->icon('heroicon-s-cog')
                ->button(),
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->url(fn() => route('proposal.pdf', $this->record->id))
                ->openUrlInNewTab(),

            Action::make('acceptProposal')
                ->label('Accept Proposal')
                ->requiresConfirmation()
                ->visible($this->record->canBeAcceptedBy())
                ->modalDescription("After accepting the proposal, all the service managers will contact you for scheduling the proposal access.")
                ->action(function () {
                    $this->record->accept();
                }),

            Action::make('discardProposal')
                ->label('Discard Proposal')
                ->requiresConfirmation()
                ->modalDescription('By discarding the proposal, it will be archived and ended. Are you sure you want to continue?')
                ->visible($this->record->canBeDiscarded())
                ->form(
                    fn(Form $form) => $form
                        ->schema([
                            Textarea::make('proposal_notes')
                                ->required()
                                ->label(__('Notes'))
                        ])
                )
                ->action(function (array $data, $record) {
                    $record->proposal_notes = $data['proposal_notes'];
                    $this->record->discard($data['proposal_notes']);
                }),

            Action::make('confirmFiles')
                ->label('Confirm files')
                ->requiresConfirmation()
                ->modalDescription('Do you confirm that all the needed files are present and correct?')

                ->visible($this->record->canBeConfirmed())
                ->form(
                    fn(Form $form) => $form
                        ->schema([
                            Textarea::make('proposal_notes')
                                ->required()
                                ->label(__('Notes'))
                        ])
                )
                ->action(function (array $data, $record) {
                    $record->proposal_notes = $data['proposal_notes'];
                    $this->record->confirm($data['proposal_notes']);
                }),

        ];
    }

    public function form(Form $form): Form
    {
        $leader = $this->record->leader->first();
        $applicants = $this->record->partners()->get();
        $applicantsEmailInputs = [];
        foreach ($applicants as $index => $user) {
            $applicantsEmailInputs[] =
                Placeholder::make('applicant')
                ->label($user->full_name)
                ->content(function () use ($user) {
                    $ret =  $user->email;
                    if ($user->pivot->alias) {
                        $ret .= ' - <b>Deputy</b>';
                    }
                    return new HtmlString($ret);
                })
                ->inlineLabel();
        }

        $servicesData = [];
        $proposalServices = $this->record->proposalServices()->get();
        foreach ($proposalServices as $item) {

            $service =  Service::find($item['service_id']);
            $servicesData[] =
                Section::make()
                ->columns(2)
                ->heading(function () use ($service,) {
                    return new HtmlString(
                        '<div class="py-3 justify-start items-center inline-flex">
                                            <div class="grow shrink basis-0 flex-col justify-start items-start inline-flex">
                                                <div class="text-gray-500 text-sm font-normal font-[\'Montserrat\'] leading-tight">
                                                    <a target="_blank" href="' . route('service', ['id' => $service->id]) . '">' .
                            $service->title .
                            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pb-1 size-6 w-5 h-5 inline-block">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                  </div>'
                    );
                })
                ->schema([
                    Placeholder::make('feasibility')
                        // ->inlineLabel()
                        ->columnSpan(1)
                        ->content(
                            function () use ($item) {

                                $content = ucfirst(str_replace('_', ' ', $item['feasible']));
                                if ($item->isNotFeasible()) {
                                    $class = 'text-red-500';
                                } else if ($item->isFeasible()){
                                    $class = 'text-green-600';
                                } else {
                                    $class = 'text-grey-600';
                                }

                                if (!$content) {
                                    $content = "To be defined";
                                }
                                $content = '<span class="' . $class . ' font-bold">' . $content . '</span>';
                                return new HtmlString($content);
                            }
                        ),
                    Placeholder::make('motivation')
                        // ->inlineLabel()
                        ->columnSpan(1)
                        ->label('Feasibility comment')
                        ->content($item['motivation'] ?? "N/A")
                        ->visible(fn(): bool => Auth::user()->hasAnyRole([User::ADMIN_ROLE, User::HELP_DESK_ROLE, User::SERVICE_MANAGER, User::REVIEWER_ROLE])),

                    Placeholder::make('access')
                        ->hidden(function () use ($item) {
                            return !$item->access;
                        })
                        ->content(function () use ($item) {
                            return ucfirst(str_replace('_', ' ', $item->access));
                        }),

                    Placeholder::make('scheduled_date')
                        ->hidden(function () use ($item) {
                            return !$item->scheduled_date;
                        })
                        ->content(function () use ($item) {
                            return $item->scheduled_date;
                        }),

                    Placeholder::make('contact_persons')
                        ->label(__('Service manager contact'))
                        ->inlineLabel()
                        ->columnSpanFull()
                        ->content(self::getContactPersons($item['service_id'])),
                ])
                ->headerActions([
                    \Filament\Forms\Components\Actions\Action::make('remove')
                        ->hidden(function () use ($item) {

                            if ($item->proposal->canServiceBeRemoved($item->service)) {
                                return false; // not hidden
                            }
                            return true;
                        })
                        ->requiresConfirmation()
                        ->modalHeading(new HtmlString('Remove service <br/>"' . $service->title . '"'))
                        ->modalDescription(
                            new HtmlString(
                                'Are you sure you want to remove the selected service from the proposal?<br/>
                             The action cannot be undone!'
                            )
                        )->action(
                            function () use ($item) {
                                // TODO: send email
                                $item->delete();
                            }
                        )
                ]);
        }


        $reviewersData = [];
        foreach ($this->record->reviewers as $reviewer) {



            if ($reviewer->status == ProposalReviewerStatus::ACCEPTED->name){
                $reviewersData[] = Placeholder::make('reviewer')
                    ->label($reviewer->reviewer->full_name)
                    ->content($reviewer->reviewer->email)
                    ->inlineLabel();
            }
        }

        $schema = [
            Section::make()
                ->heading(fn(Get $get): ?string => $get('name'))
                ->schema([
                    TextInput::make('id')
                        ->inlineLabel()
                        ->label(__('ID proposal')),
                    DateTimePicker::make('published_at')
                        ->label(__('Submission date'))
                        ->inlineLabel(),
                    Placeholder::make('leader')
                        ->label(__('User group leader'))
                        ->content($leader?->full_name . ', ' . $leader?->email)
                        ->inlineLabel(),
                ]),
            Section::make()
                ->heading(__('Partners'))
                ->schema([
                    ...$applicantsEmailInputs
                ]),
        ];
        $schema[] = Section::make()
            ->heading(__('Services'))
            ->schema([
                ...$servicesData
            ]);

        if (Auth::user()->hasPermissionTo('administer proposals')) {
            $schema[] = Section::make()
                ->heading(__('Reviewers'))
                ->schema([
                    ...$reviewersData
                ]);
        }

        return $form->disabled()->schema($schema);
    }

    public static function getContactPersons($service_id): HtmlString
    {
        $tmp = [];
        foreach (Service::find($service_id)->serviceManagers as $serviceManager){
            $tmp []= $serviceManager->full_name . ' (' . $serviceManager->email . ')';
        }

        return new HtmlString(implode('<br/>', $tmp));
    }
}
