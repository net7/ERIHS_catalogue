<?php

namespace App\Filament\Resources\CommonProposalResource\Pages;

use App\Enums\ProposalReviewerStatus;
use App\Filament\Resources\ProposalResource;
use App\Filament\Resources\ProposalReviewerResource;
use App\Mail\ReviewerDeleted;
use App\Mail\ReviewerSelected;
use App\Models\Proposal;
use App\Models\User;
use App\ProposalStatusActivities\ReviewerSelectionStatusActivity;
use App\Services\ERIHSMailService;
use App\Services\ProposalService;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class ManageReviewers extends Page implements HasForms, HasTable
{
    use HasPageSidebar;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $view = 'filament.resources.proposal-resource.pages.manage-proposal-reviewers';

    protected static ?string $breadcrumb = "Manage reviewers";
    protected static ?string $title = "Manage reviewers";


    public function table(Table $table): Table
    {
        $record = $this->record;
        $canAddReviewer = self::canAddReviewer($record);
        $reviewers = ProposalService::queryGetReviewers($record->id)->get();

        return ProposalReviewerResource::table($table)
            ->query(fn () => $record->reviewers()->getQuery())
            ->headerActions([
                CreateAction::make()
                    ->label('Add reviewer')
                    ->form(
                        fn (Form $form) => $form
                            ->schema([
                                Select::make('reviewer_id')
                                    ->label('Reviewer')
                                    ->options($reviewers->pluck('email', 'id')->toArray())
                                    ->searchable()
                                    ->disabled(!$canAddReviewer)
                                    ->required()
                            ])
                    )
                    ->action(function (array $data) use ($record) {
                        $record->reviewers()->create([
                            'reviewer_id' => $data['reviewer_id'],
                            'status' => ProposalReviewerStatus::TO_BE_CONFIRMED->name,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        Notification::make()
                            ->title('Success')
                            ->body('Reviewer added successfully.')
                            ->success()
                            ->send();
                    })
                    ->modalSubmitActionLabel('Add')
                    ->modalHeading('Select a reviewer')
                    ->createAnother(false)
                    ->disabled(!$canAddReviewer)
                    ->visible($canAddReviewer),
            ])->actions([
                Action::make('confirmReviewer')
                    ->label('Confirm Reviewer')
                    ->action(function ($record) {
                        $reviewer_id = $record->reviewer_id;
                        $user = User::find($reviewer_id);
                        $record->update([
                            'status' => ProposalReviewerStatus::WAITING->name,
                            'updated_at' => now(),
                            'confirmed_at' => now()
                        ]);

                        /*$user->number_of_reviews = $user->number_of_reviews - 1;
                        if ($user->number_of_reviews < 0) {
                            $user->number_of_reviews = 0;
                        }
                        $user->save();*/
                        Notification::make()
                            ->title('Success')
                            ->body('Reviewer confirmed successfully.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === ProposalReviewerStatus::TO_BE_CONFIRMED->name)
                    ->requiresConfirmation(),
                DeleteAction::make()
                    ->label('Remove')
                    ->action(function ($record) {
                        if ($record->status === ProposalReviewerStatus::WAITING->name) {
                            $reviewer_id = $record->reviewer_id;
                            $user = User::find($reviewer_id);
                            $user->number_of_reviews = $user->number_of_reviews + 1;
                            $user->save();
                            $record->delete();
                        } else {
                            $record->delete();
                        }
                        // reload the page and show the "add Reviewer" button if needed
                        $this->resetTable();
                    })
                    ->visible(fn ($record) => $record->status === ProposalReviewerStatus::TO_BE_CONFIRMED->name || $record->status === ProposalReviewerStatus::WAITING->name),
            ]);
    }


    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }


    public static function sidebar(Proposal $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->setNavigationItems(ProposalResource::navigationItems($record));
    }

    public static function canAddReviewer($record): bool
    {
        $reviewers = $record->reviewers()->get();
        $refused = 0;
        foreach ($reviewers as $reviewer) {
            if ($reviewer->status == ProposalReviewerStatus::REFUSED->name) {
                $refused++;
            }
        }
        return count($reviewers) - $refused < 3;
    }
}
