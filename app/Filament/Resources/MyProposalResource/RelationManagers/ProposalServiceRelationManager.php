<?php

namespace App\Filament\Resources\MyProposalResource\RelationManagers;

use App\Filament\Resources\MethodResource;
use App\Filament\Resources\ProposalReviewerResource;
use App\Filament\Resources\ToolResource;
use App\Models\Method;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Tool;
use Filament\Forms\Components\Checkbox;
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

class ProposalServiceRelationManager extends RelationManager
{
    protected static string $relationship = 'proposalServices';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Checkbox::make('feasible')->label('Feasible?')
            ]);
    }
}
