<?php

namespace App\Filament\Resources\CallResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProposalsRelationManager extends RelationManager
{
    protected static string $relationship = 'proposals';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('Proposal Title')
                    ->content(function ($record){
                        return $record->name;
                    })
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('partners')
                    ->relationship('applicants')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Placeholder::make('name')
                            ->content(function ($record){
                                return $record->full_name;
                            })
                            ->columnSpan(1),
                        Forms\Components\Placeholder::make('email')
                            ->content(function ($record){
                                return $record->email;
                            })
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('leader/Alias')
                            ->content(function ($record){
                                if ($record->leader){
                                    return 'Leader' ;
                                }
                                if ($record->alias){
                                    return 'Alias' ;
                                }
                                return 'No';
                            })
                            ->columnSpan(1),
                        ])
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('services')
                        ->relationship('services')
                        ->columns(3)
                        ->schema([
                            Forms\Components\Placeholder::make('title')
                                ->content(function ($record){
                                    return $record->title;
                                })
                                ->columnSpan(2),
                            Forms\Components\Placeholder::make('organization')
                                ->content(function ($record){
                                    return $record->organization->name;
                                })
                                ->label('Organization')
                                ->columnSpan(1),
                        ])
                        ->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Proposal Title')
                    ->words(10)
                    ->tooltip(fn($record) => $record->name),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Manage')
                    ->url(fn ($record):string => route('filament.app.resources.proposals.general-info', $record))
                    ->icon('heroicon-o-cog')
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
