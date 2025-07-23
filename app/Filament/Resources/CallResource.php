<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallResource\Pages;
use App\Filament\Resources\CallResource\RelationManagers;
use App\Models\Call;
use App\Services\CallService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class CallResource extends Resource
{
    protected static ?string $model = Call::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Applications';
    protected static ?string $recordTitleAttribute = 'name';
    public static function form(Form $form): Form
    {


        return $form
            ->schema([

                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->rules([
                        fn($get, ?Model $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                            if (CallService::checkIfDatesOverlap($get('start_date'), $get('end_date'), $record?->id)) {
                                $fail("The dates overlap with existing calls!");
                            }
                        },
                    ])
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state)  {
                        $set('end_date', $state);
                    })->live(onBlur: true)
                    ,
                Forms\Components\DatePicker::make('end_date')->required()
                    ->rules([
                        fn($get, ?Model $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                            if (CallService::checkIfDatesOverlap($get('start_date'), $get('end_date'), $record?->id)) {
                                $fail("The dates overlap with existing calls!");
                            }
                        },
                    ])
                    ->afterOrEqual('start_date'),
                // Forms\Components\FileUpload::make('form_pdf_path')
                //     ->label(__('Form PDF'))
                //     ->downloadable()
                //     ->previewable(false) // to show the actual file name in the form
                //     ->acceptedFileTypes(['application/pdf']),
                // Forms\Components\FileUpload::make('call_pdf_path')
                //     ->label(__('Call PDF'))
                //     ->downloadable()
                //     ->previewable(false) // to show the actual file name in the form
                //     ->acceptedFileTypes(['application/pdf']),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('proposals_count')
                    ->label('Proposals')
                    ->counts('proposals')
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                //
            ])
            
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProposalsRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalls::route('/'),
            'create' => Pages\CreateCall::route('/create'),
            'edit' => Pages\EditCall::route('/{record}/edit'),
        ];
    }
}
