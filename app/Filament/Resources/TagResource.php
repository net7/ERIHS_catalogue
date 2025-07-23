<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Services\TagsService;
use Spatie\Tags\Tag;
use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationGroup = 'Settings';

    public static function canCreate(): bool
   {
      return false;
   }
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                KeyValue::make('name')->columnSpanFull(),
                KeyValue::make('slug')->columnSpanFull()->disabled(),
                TextInput::make('type')->disabled(),
                TextInput::make('external_id')->disabled(),
                Placeholder::make('used_by')
                    ->content(
                        function ($record) {
                            $res = [];
                            $models = TagsService::getUsage($record);
                            foreach ($models as $modelClass => $modelIds) {

                                foreach ($modelIds as $modelId){
                                    $instance = $modelClass::find($modelId);
                                    $title = isset($instance->name)? $instance->name: $instance->title;
                                    if ($modelClass == 'App\Models\User'){
                                        $title = $instance->getFilamentName() . ' ('. $instance->email . ')';
                                    } elseif($modelClass == 'App\Models\Method'){
                                        $title = $instance->preferred_label;
                                    }
                                    $res []= "<b>" .substr($modelClass,strrpos($modelClass, '\\') +1 ) . "</b>: " . $title;
                                }
                            }
                            if (empty($res)){
                                return new HtmlString("Nothing");
                            }
                            return new HtmlString(implode("<br/>", $res));
                        }
                    )->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('type'),
                TextColumn::make('external_id'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                ->label('Type')
                ->options(
                    Tag::getTypes()->mapWithKeys(
                        function ($tag) { return [$tag => $tag];}
                        )
                    )
                ->query(function (Builder $query, $data) {
                    if (!$data['value']){
                        return $query;
                    }
                    return $query->where('type', $data);
                })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
