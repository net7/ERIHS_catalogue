<?php

namespace App\Filament\Resources\ProposalReviewerResource\RelationManagers;

use App\Filament\Resources\MethodResource;
use App\Filament\Resources\ProposalReviewerResource;
use App\Filament\Resources\ToolResource;
use App\Models\Method;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Tool;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;

class ProposalReviewerRelationManger extends RelationManager
{
    protected static string $relationship = 'reviewer';

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('reviewer_id')
                    ->preload()
                    ->searchable()
                    ->relationship('reviewers')
                    ->createOptionForm(ProposalReviewerResource::formSchema())
                    ->columnSpanFull()
                    ->required()->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, callable $get, RelationManager $livewire) {
                        return $rule
                            ->where('reviewer_id', $get('reviewer_id'))
                            ->where('proposal_id', $livewire->getOwnerRecord()?->id);
                    })

            ]);
    }
}
