<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailTemplateResource\Pages;
//use App\Filament\Resources\MailTemplateResource\RelationManagers;
//use App\Models\MailTemplate;
use Rawilk\FilamentQuill\Enums\ToolbarButton;
use Spatie\MailTemplates\Models\MailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function Laravel\Prompts\text;
use Rawilk\FilamentQuill\Filament\Forms\Components\QuillEditor;

class MailTemplateResource extends Resource
{
    protected static ?string $model = MailTemplate::class;

    // protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {

        $placeholders = $form->getModelInstance()->getVariables();
        unset($placeholders[array_search('logo_url', $placeholders)]);
        // dd($placeholders);
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->columnSpan('full')->disabledOn('edit'),
                Forms\Components\TextInput::make('mailable')->columnSpan('full'),
                Forms\Components\TextInput::make('subject')->columnSpan('full'),
                QuillEditor::make('html_template')
                    ->placeholders($placeholders)
                    ->surroundPlaceholdersWith(start: '{{ ', end: ' }}')
                    ->disableToolbarButtons([
                        ToolbarButton::Image,
                        ToolbarButton::Scripts,
                    ])
                    ->columnSpan('full'),

                // Forms\Components\RichEditor::make('html_template')->columnSpan('full')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMailTemplates::route('/'),
            'create' => Pages\CreateMailTemplate::route('/create'),
            'edit' => Pages\EditMailTemplate::route('/{record}/edit'),
        ];
    }
}
