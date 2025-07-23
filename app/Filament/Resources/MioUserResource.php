<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MioUserResource\Pages;
use App\Filament\Resources\MioUserResource\RelationManagers;
use App\Filament\Resources\MioUserResource\RelationManagers\RoleRelationManager;
use App\Models\User;
use App\Models\UserProxyModel;
use App\Observers\UserObserver;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Phpsa\FilamentAuthentication\Actions\ImpersonateLink;
use Spatie\Permission\Models\Role;

class MioUserResource extends Resource
{
    // protected static ?string $model = UserProxyModel::class;
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'User';
    protected static ?string $navigationGroup = 'Roles assignement';

    protected static ?string $navigationLabel = 'User Roles ';
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function canViewAny(): bool
    {
        return Gate::allows('view-user-roles');
    }

    public static function canEdit(Model $record): bool
    {
        return Gate::allows('view-user-roles');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        TextInput::make('name')
                            ->label(strval(__('filament-authentication::filament-authentication.field.user.name')))
                            ->required()
                            ->disabled(),
                        TextInput::make('surname')
                            ->label(strval(__('Surname')))
                            ->required()
                            ->disabled(),
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(table: static::$model, ignorable: fn($record) => $record)
                            ->label(strval(__('filament-authentication::filament-authentication.field.user.email')))
                            ->disabled(),
                    ]),
                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->options(function () {
                        return Role::where('name', '!=', User::ADMIN_ROLE)->pluck('name', 'id');
                    })
                    ->label(__('Roles'))
                    ->afterStateUpdated(function (Get $get, ?array $state) {

                        // This would run as soon as the values are changed (before saving)
                        $userId = $get('id');
                        $user = User::find($userId);

                        $user->syncRoles($state);

                        $observer = new UserObserver();
                        $observer->updated($user);
                    })
                    // ->loadStateFromRelationshipsUsing(function ($component, $record) {
                    //     $component->state($record->rolesIds());
                    // })
                    // ->saveRelationshipsUsing(fn ($component) => $component->saveUploadedFiles())
                    ->live(debounce: 50),
            ]);
    }

    public static function table(Table $table): Table
    {


        $actions = [
            Tables\Actions\EditAction::make()->label(__('Edit roles')),
            ImpersonateLink::make(),
        ];


        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->label(strval(__('filament-authentication::filament-authentication.field.id'))),
                TextColumn::make('full_name')
                    ->searchable()
                    ->sortable()
                    ->label(strval(__('filament-authentication::filament-authentication.field.user.name'))),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label(strval(__('filament-authentication::filament-authentication.field.user.email'))),
                TextColumn::make('roles.name')->badge()
                    ->label(strval(__('filament-authentication::filament-authentication.field.user.roles'))),
            ])
            ->filters([
                //
            ])
            ->actions(
                $actions,
            )
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    UserResource::getExportBulkAction(),
                // ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
