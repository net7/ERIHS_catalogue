<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Phpsa\FilamentAuthentication\Resources\UserResource as BaseUserResource;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Spatie\Permission\Models\Role;

class UserResource extends BaseUserResource
{
    protected static ?string $model = User::class;

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->email;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name' => $record->full_name,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'surname', 'email'];
    }


    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return UserResource::getUrl('edit', ['record' => $record]);
    }
    protected static ?string $recordTitleAttribute = 'email';

    protected function getActions(): array
    {
        return [
            Action::make('generateToken')
                ->label('Generate API Token')
                ->action(function () {
                    $record = $this->record;
                    $token = $record->createToken('auth_token')->plainTextToken;
                    $record->api_token = $token;
                    $record->save();
                    $this->notify('success', 'API Token generated successfully: ' . $token);
                })
                ->visible(fn($livewire) => auth()->user()->can('administer users')),
        ];
    }

    public static function form(Form $form): Form
    {

        return $form
        ->schema([
            Section::make()
                ->schema([
                    TextInput::make('name')
                        ->label(strval(__('filament-authentication::filament-authentication.field.user.name')))
                        ->required(),
                    TextInput::make('surname')
                        ->label(strval(__('Surname')))
                        ->required(),
                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->unique(table: static::$model, ignorable: fn ($record) => $record)
                        ->label(strval(__('filament-authentication::filament-authentication.field.user.email'))),
                    TextInput::make('password')
                        ->same('passwordConfirmation')
                        ->password()
                        ->minLength(8)
                        ->maxLength(255)
                        ->required(fn ($component, $get, $livewire, $model, $record, $set, $state) => $record === null)
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->label(strval(__('filament-authentication::filament-authentication.field.user.password')))
                        
                        ,
                    TextInput::make('passwordConfirmation')
                        ->password()
                        ->dehydrated(false)
                        ->minLength(8)
                        ->maxLength(255)
                        ->label(strval(__('filament-authentication::filament-authentication.field.user.confirm_password'))),
                    Select::make('roles')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload(config('filament-authentication.preload_roles'))
                        ->label(strval(__('filament-authentication::filament-authentication.field.user.roles'))),
                    Select::make('organizations')
                        ->multiple()
                        ->relationship('organizations', 'name')
                        ->label('Organizations')
                        ->columnSpanFull()
                        ->visible(fn($livewire) => auth()->user()->can('administer users'))
                        ->disabled(),
                    Forms\Components\Placeholder::make('api_token')
                        ->content(fn($record) => $record != null ? $record->api_token :'')
                        ->label('API Token')
                        ->visible(fn($livewire) => auth()->user()->can('administer users')),

                ])->columns(2),
        ]);
    }

    private static function getApiToken($record)
    {
        // Recupera i token dell'utente
        $tokens = PersonalAccessToken::where('tokenable_id', $record->id)
            ->where('tokenable_type', get_class($record))
            ->get();

        if ($tokens->isEmpty()) {
            return 'No token available';
        }

        // Mostra il primo token trovato o modifica secondo le tue necessità
        return $tokens->first()->plainTextToken;
    }

    public static function table(Table $table): Table
    {

        $table = parent::table($table);
        $parentTableColumns = $table->getColumns();
        unset($parentTableColumns['name']);
        unset($parentTableColumns['created_at']);
        $table->columns([
            $parentTableColumns['id'],
            TextColumn::make('full_name')
                ->label('Name')
                ->sortable()
                ->searchable(),
            $parentTableColumns['email']->limit(25)->tooltip(
                function (TextColumn $column): ?string {
                $state = $column->getState();

                if (strlen($state) <= $column->getCharacterLimit()) {
                    return '';
                }
                return $state;
            }),
            $parentTableColumns['email_verified_at'],
            $parentTableColumns['roles.name'],
            TextColumn::make('organizations')
                ->badge()
                ->label('Organizations')
                ->wrap()
                ->formatStateUsing(function ($state) {
                    if ($state && $state != '') {
                        $organizations = json_decode($state);
                        if ($organizations && isset($organizations->name)) {
                            return $organizations->name;
                        }
                    }
                    return '';
                })
                ->sortable(false),
        ]);
        $rolesFilter = Tables\Filters\SelectFilter::make('role')
            ->label('Role')
            ->options(Role::all()->pluck('name', 'id'))
            ->query(function (Builder $query, $data) {
                if (!$data['value']) {
                    return $query;
                }
                return $query->whereHas('roles', function ($q) use ($data) {
                    $q->where('model_has_roles.role_id', $data);
                });
            });

        $organizationsFilter = Tables\Filters\SelectFilter::make('organizations')
            ->label('Organizations')
            ->options(Organization::all()->pluck('name', 'id'))
            ->query(function (Builder $query, $data) {
                if (!$data['value']) {
                    return $query;
                }
                return $query->whereHas('organizations', function ($q) use ($data) {
                    $q->where('organization_id', $data);
                });
            });

        $table->filters([...$table->getFilters(), $rolesFilter, $organizationsFilter]);

        $table->bulkActions([
            self::getExportBulkAction(),
                ]);

        return $table;

    }

    public static function getExportBulkAction()
    {
        return ExportBulkAction::make('export')
            ->exports([
                ExcelExport::make('form')
                    ->fromForm()
                    ->except([
                        'password',
                        'passwordConfirmation',
                        'api_token',
                        'created_at',
                        'updated_at',
                    ])
                    ->withFilename('Users_' . date('Y-m-d'))
                    ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                    ->withColumns([
                        Column::make('roles')
                            ->heading('Roles')
                            ->formatStateUsing(function ($state) {
                                return $state->pluck('name')->implode(', ');
                            }),
                        Column::make('organizations')
                            ->heading('Organizations')
                            ->formatStateUsing(function ($state) {
                                return $state->pluck('name')->implode(', ');
                            }),
                    ])
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
