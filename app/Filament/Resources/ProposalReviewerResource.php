<?php

namespace App\Filament\Resources;

use App\Enums\ProposalReviewerRefusalReason;
use App\Enums\ProposalReviewerStatus;
use App\Filament\Resources\ProposalReviewerResource\Pages;
use App\Models\ProposalReviewer;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProposalReviewerResource extends Resource
{
    protected static ?string $model = ProposalReviewer::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reviewer.full_name')
                    ->description(fn($record): string => $record->reviewer->email)
                    ->label('Reviewer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => ProposalReviewerStatus::options()[$state]),
                Tables\Columns\TextColumn::make('refused_reason')
                    ->formatStateUsing(fn (string $state): string => ProposalReviewerRefusalReason::options()[$state]),
                Tables\Columns\TextColumn::make('refused_comment'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProposalReviewerResource\RelationManagers\ProposalReviewerRelationManger::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProposalReviewers::route('/'),
            'create' => Pages\CreateProposalReviewer::route('/create'),
            'edit' => Pages\EditProposalReviewer::route('/{record}/edit'),
        ];
    }
}
