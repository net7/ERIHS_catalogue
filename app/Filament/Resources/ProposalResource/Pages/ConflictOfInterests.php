<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Enums\ProposalReviewerRefusalReason;
use App\Enums\ProposalReviewerStatus;
use App\Filament\Resources\ProposalResource;
use App\Mail\NoMoreReviewsAvailable;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;
use App\Services\ERIHSMailService;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ConflictOfInterests extends Page
{
    use HasPageSidebar;
    use InteractsWithRecord;

    protected static string $resource = ProposalResource::class;
    protected static string $view = 'filament.resources.proposal-resource.pages.conflict-of-interests';

    protected static ?string $breadcrumb = "Conflict of interests";
    protected static ?string $title = "Conflict of interests";
    public $acceptance;
    public $refused_reason;
    public $refused_comment;
    public $proposal_reviewer;

    public function getFormSchema(): array
    {
        return [
            Radio::make('acceptance')
                ->live()
                ->label(__('Please select one of the following options:'))
                ->required()
                ->live()
                ->inline()
                ->inlineLabel(false)
                ->options([
                    'accept' => __('I here by declare that no conflict of interest is present and I will proceed with its assessment'),
                    'conflict_of_interest' => __('I here by declare that conflicts of interest are present and I will NOT proceed with its assessment'),
                    'reject' => __('I here by declare that I will NOT proceed with its assessment')
                ])
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state === 'conflict_of_interest') {
                        $set('refused_reason', 'CONFLICT_OF_INTEREST');
                    }
                    if ($state === 'reject') {
                        $set('refused_reason', 'EXPLICIT_REFUSAL');
                    }
                })
                ->disabled(fn(): bool => $this->proposal_reviewer->accepted_at != null || $this->proposal_reviewer->refused_at != null),
            Select::make('refused_reason')
                ->label('Refusal reason')
                ->live()
                ->options(ProposalReviewerRefusalReason::options())
                ->hidden()
                ->dehydratedWhenHidden()
                ,
            Textarea::make('refused_comment')
                ->label('Refusal comment')
                ->hidden(fn(Get $get): bool => $get('acceptance') == 'accept')
                ->required(fn($get): bool => $get('acceptance') != 'accept')
                ->disabled(fn():bool => $this->proposal_reviewer->accepted_at != null ||  $this->proposal_reviewer->refused_at != null),
            ViewField::make('submit')
                ->view('submit')
                ->hidden(fn():bool => $this->proposal_reviewer->accepted_at != null ||  $this->proposal_reviewer->refused_at != null)
        ];
    }

    public function form(Form $form): Form
    {

        return $form->schema(self::getFormSchema());
    }


    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->proposal_reviewer = $this->record->reviewers()
            ->where('reviewer_id', '=', Auth::user()->id)
            ->where('proposal_id', '=', $this->record->id)
            ->first();
        $this->acceptance = $this->proposal_reviewer->status == ProposalReviewerStatus::REFUSED->name ? 'reject' : 'accept';
        $this->refused_reason = $this->proposal_reviewer->refused_reason;
        $this->refused_comment = $this->proposal_reviewer->refused_comment;
    }


    public static function sidebar(Proposal $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->setNavigationItems(ProposalResource::navigationItems($record));
    }

    public function submit()
    {
        $user = Auth::user();

        $proposalReviewer = ProposalReviewer::where('reviewer_id', '=', $user->id)
            ->where('proposal_id', '=', $this->record->id)->first();

        $data = [
            'acceptance' => $this->acceptance,
            'refused_reason' => $this->refused_reason,
            'refused_comment' => $this->refused_comment
        ];
        validator($data, [
            'acceptance' => 'required|string',
            'refused_comment' => 'required_unless:acceptance,accept',
        ])->validate();
        if ($data['acceptance'] == 'accept') {
            $proposalReviewer->status = ProposalReviewerStatus::ACCEPTED->name;
            $proposalReviewer->refused_reason = null;
            $proposalReviewer->refused_comment = null;
            $proposalReviewer->accepted_at = now();
            $proposalReviewer->refused_at = null;
            $user->number_of_reviews = max(0,  $user->number_of_reviews - 1);
            if($user->number_of_reviews == 0) {
                Mail::to($user->email)->send(new NoMoreReviewsAvailable());
            }
            $user->update();

        } else {
            $proposalReviewer->status = ProposalReviewerStatus::REFUSED->name;
            $proposalReviewer->refused_reason = $data['refused_reason'];
            $proposalReviewer->refused_comment = $data['refused_comment'];
            $proposalReviewer->accepted_at = null;
            $proposalReviewer->refused_at = now();
        }
        //self::sendEmailToUH($data['acceptance'], $user_id, $this->record->id);
        Notification::make()
            ->title('Success')
            ->body('Preferences saved')
            ->success()
            ->send();
        $proposalReviewer->save();
        return $data['acceptance'] == 'accept' ? redirect()->to('dashboard/my-proposals/'. $this->record->id. '/evaluation')
            : redirect()->to(route('dashboard'));

    }

    public static function sendEmailToUH($status, $reviewerId, $proposalId)
    {

        $reviewer = User::find($reviewerId);
        $proposal = Proposal::find($proposalId);
        $mailService = new ERIHSMailService();
        if ($status == 'reject') {
            $mailService->reviewerExplicitRefusal($reviewer, $proposal);
        }
        if ($status == 'accept') {
            $mailService->reviewerAcceptance($reviewer, $proposal);
        }
        if ($status == 'conflict_of_interest') {
            $mailService->reviewerConflicts($reviewer, $proposal);
        }
    }
}
