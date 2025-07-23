<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MethodResource\Pages;
use App\Models\Method;
use App\Models\Organization;
use App\Models\User;
use App\Services\MethodService;
use App\Services\TagsService;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\ExcludeIf;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Spatie\Tags\Tag;

class MethodResource extends Resource
{
    protected static ?string $model = Method::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 7;

    public static function getEloquentQuery(): Builder
    {
        if (\auth()->user()->hasPermissionTo('administer site') ||
            \auth()->user()->hasPermissionTo('administer methods')) {
            return parent::getEloquentQuery();
        }
        return MethodService::getMyMethods();
    }

    public static function formSchema()
    {
        return [
            TextInput::make('preferred_label')
                ->label('Title')
                ->maxLength(255)
                ->required(),
            Select::make('organization_id')
                ->disabled(fn ($livewire) =>
                    $livewire instanceof \Filament\Resources\Pages\EditRecord  && 
                    !auth()->user()->hasAnyRole([User::ADMIN_ROLE, User::HELP_DESK_ROLE]) 
                    )
                ->relationship('organization', 'name')
                ->options(function () {
                    $user = auth()->user();
                    if (auth()->user()->hasPermissionTo('administer site') ||
                        auth()->user()->hasPermissionTo('administer methods')) {
                        return Organization::all()->pluck('name', 'id');
                    } else {
                        return $user->organizations()->pluck('name', 'organization_id');
                    }
                })
                ->required(),
            Textarea::make('method_documentation')
                ->maxLength(16777215)
                ->columnSpan('full')
                ->label('Description'),
            TagsService::tagsGrid(
                'technique',
                'technique',
                'Relevant Technique',
                required: true,
                multiple: false,
                searchable: true,
                hintIcon: 'heroicon-m-question-mark-circle',
                hintTooltip: "The general technique that this Method is an application of. If none is applicable, choose 'other' and specify the technique in the 'Other Techniques' field."
            ),
            TextInput::make('technique_other')
                ->maxLength(255)
                ->label('Other Techniques (if necessary)')
                ->hintIcon('heroicon-m-question-mark-circle', 'If the technique is not in the controlled vocabulary, please specify it here.'),
            TextInput::make('method_type')
                ->maxLength(255)
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                     tooltip: 'Specify here the type that better describes your method. Examples are: \'Analysis Method\', \'Research support\', \'Data processing\', \'Archival method\', etc.. '
                    ),
            TextInput::make('method_version')
                ->maxLength(255)
                ->hintIcon('heroicon-m-question-mark-circle', 'e.g: 1.0.4, 2-alpha-324')
                ->required(),
            Repeater::make('alternative_labels')
                ->schema([
                    TextInput::make('alternative_codes')
                        ->required()
                        ->label('Alternative codes, labels or names')
                ])
                ->columnSpanFull()
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                    'Any alternative codes, label or names for the method (Not for the technique or tools). This could be an acronym or a full text name. If an acronym, it is used as the preferred label. This is used for search and discovery purposes'
                ),
            Repeater::make('method_parameter')
                ->columnSpan('full')
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                    'A list of the core or key equipment or software parameters used within this method. This does not need to be an exhaustive list and can be limited to the parameters which are used to control the activity, might influence subsequent interpretations or will be important for data comparison and discovery'
                )
                ->schema(
                    [
                        TagsService::tagsSchemaForRepeater(
                            'parameter_type_tag_field',
                            'method_parameter_type',
                            'Parameter type',
                            required: true,
                            multiple: false,
                            searchable: true,
                            preload: true
                        ),
                        Select::make('parameter_value_type')
                            ->label('Value unit')
                            ->options(['number' => 'Number', 'string' => 'String', 'boolean' => 'Boolean'])
                            ->columns(1)
                            ->required()
                            ->afterStateUpdated(function ($set) {
                                $set('parameter_value', null);
                            })
                            ->live(),
                        TextInput::make('parameter_value')
                            ->label('Value')
                            ->hint('Core method Value')
                            ->columns(2)
                            ->hidden(function ($get) {
                                return $get('parameter_value_type') == 'boolean';
                            })
                            ->dehydratedWhenHidden(false)
                            ->numeric(fn($get) => $get('parameter_value_type') == 'number')

                            ->required(),
                        Radio::make('parameter_value')
                            ->label('Value')
                            ->hint('Core method Value')
                            ->boolean()
                            ->inline()
                            ->inlineLabel(false)
                            ->visible(function ($get) {
                                return $get('parameter_value_type') == 'boolean';
                            })
                            ->dehydratedWhenHidden(false)
                            ->required(),
                        TagsService::tagsSchemaForRepeater(
                            'parameter_unit_tag_field',
                            'method_parameter_unit',
                            'Parameter Unit',
                            required: true,
                            multiple: false,
                            searchable: true,
                            preload: true,
                            hintIcon: 'heroicon-m-question-mark-circle',
                            hintTooltip: 'The unit in which the value is expressed. The dropdown list gives a few examples, but if required, you can define your own component clicking on +    '

                        ),

                        TextInput::make('parameter_related_tool')
                            ->label('Parameter Specific Related Tool')
                            ->hintIcon(
                                'heroicon-m-question-mark-circle',
                                'When multiple tools are used within a single method, defined parameters can be associated to the relevant tool. This can be left blank for single tool'
                            ),
                    ]
                )
                ->columns(),
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::formSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('preferred_label')
                    ->searchable()
                    ->label('Name'),
                TextColumn::make('organization.name'),
                TextColumn::make('organization')
                    ->label('Countries')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            return collect($state->getCountries())->implode(', ');
                        }
                        return '';
                    })
                    ->limit(25)
                    ->tooltip(function ($state) {
                        if ($state) {
                            return collect($state->getCountries())->implode(', ');
                        }
                        return '';
                    }),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('organization_id')
                    ->label('Organization')
                    ->preload()
                    ->options(function () {
                        if (auth()->user()->hasRole(User::SERVICE_MANAGER)) {
                            return auth()->user()->organizations()->pluck('name', 'organization_id');
                        }
                        return Organization::all()->pluck('name', 'id');
                    })
                    ->multiple(),

                Tables\Filters\SelectFilter::make('country')
                    ->label('Countries')
                    ->options(
                        function () {

                        $data= Tag::where('type', 'country')
                            ->whereIn('id', function ($query) {
                                $query->select('tag_id')
                                    ->from('taggables')
                                    ->join('tags', 'tags.id', '=', 'taggables.tag_id')
                                    ->where('taggable_type', Organization::class)
                                    ->orderBy('tags.name')
                                    ;
                            })
                            ->pluck('name', 'name')
                            ->toArray()
                            ;
                        asort($data);
                        return $data;
                        }
                    )
                    ->query(function ($query, $data) {
                        if (!empty($data['values'])) {
                            $query->whereHas('organization', function ($query) use ($data) {
                                $query->whereHas('tags', function ($query) use ($data) {
                                    $query->where('type', 'country')
                                        ->whereIn('name->en', array_map(function ($value) {
                                            return collect(json_decode($value, true))['en'];
                                        }, $data['values']));
                                });
                            });
                        }
                    })
                    ->multiple()
                    ,
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make('export')
                ->exports([
                    ExcelExport::make('form')
                        ->withFilename('Methods_' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            // ->only([
                            //     'preferred_label',
                            //     'organization_id',
                            // ])
                        ->except([
                            'method_parameter',
                            'technique',
                        ])
                            
                        ->fromForm()
                        ->ignoreFormatting([
                        ])
                        ->withColumns([
                            Column::make('organization_id')
                                ->heading('Organization')
                                ->formatStateUsing(function ($state) {
                                    return  Organization::find($state)->name;
                                }),
                        ])
                        ->modifyQueryUsing(fn(Builder $query, $livewire) => $query
                        ->whereIn('id', $livewire->selectedTableRecords)
                        ->with(['organization'])
                        ),
                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMethods::route('/'),
            'create' => Pages\CreateMethod::route('/create'),
            'edit' => Pages\EditMethod::route('/{record}/edit'),
        ];
    }
}
