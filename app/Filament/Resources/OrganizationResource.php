<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Models\Organization;
use App\Models\User;
use App\Services\RorService;
use App\Services\TagsService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Spatie\Tags\Tag;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;


    protected static ?int $navigationSort = 5;


    protected static ?string $navigationGroup = 'Settings';


    public static function getEloquentQuery(): Builder
    {
        if (\auth()->user()->hasPermissionTo('administer site')  ||
            \auth()->user()->hasPermissionTo('administer organizations')) {
            return parent::getEloquentQuery();
        }

        $user = auth()->user();
        if (!$user) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()
            ->whereHas('users', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
    }


    public static function formSchema()
    {

        $rorService = new  RorService();
        return [
            Select::make('name')
                ->searchable()
                ->getSearchResultsUsing(function ($query, callable $set) use ($rorService): array {
                    $data = $rorService->retrieveOrganizationsByName($query);
                    $set('organization_options_names', $data['names']);
                    $set('organization_options_acronyms', $data['acronyms']);

                    return $data['names'];
                })
                ->afterStateUpdated(
                    function ($state, callable $set, callable $get) use ($rorService) {
                        $names = $get('organization_options_names');
                        $acronyms = $get('organization_options_acronyms');
                        $set('name', $names[$state] ?? null);
                        $set('acronym', $acronyms[$state] ?? null);
                    }
                )
                ->createOptionForm([
                    TextInput::make('name')
                        ->required(),
                ])
                ->createOptionUsing(function (array $data, $set) {
                    if (isset($data['name'])) {
                        $set('organization_options_names', [$data['name'] => $data['name']]);
                        $set('organization_options_acronyms', [$data['name'] => $data['name']]);
                        $set('name', $data['name']);
                        return  $data['name'];
                    }
                })
                ->live(debounce: 800)
                // ->extraAttributes(['onchange' => 'this.dispatchEvent(new Event("input"))'])
                ->required(),
            TextInput::make('acronym')
                ->maxLength(255)
                ->required(),
            TextInput::make('mbox')
                ->maxLength(255)
                ->label('Email of the organization')
                ->email(),
            TextInput::make('phone')
                ->maxLength(255),
            TextInput::make('img_url')
                ->maxLength(255)
                ->hintIcon('heroicon-m-question-mark-circle', 'URL to an image representing the organization')
                ->label('Logo URL'),
            TagsService::tagsGrid('based_in', 'country', 'Based in', required: false, multiple: true, searchable: true),


            TagsService::tagsGrid(
                name: 'organization_type',
                type: 'organisation_type',
                label: 'Organization type',
                required: true,
                multiple: true,
                // addable: true,
            ),


            DatePicker::make('joined_the_field_date')
                ->hintIcon('heroicon-m-question-mark-circle', 'When did this organization start working in this field?'),
            TagsService::tagsGrid(
                name: 'research_disciplines',
                type: 'research_disciplines',
                label: 'Fields of application',
                required: true,
                multiple: true,
                searchable: true,
                hintIcon: 'heroicon-m-question-mark-circle',
                hintTooltip: 'Specify the domains or disciplines this organization has experience in. This field is connected to an extensive controlled list'
            ),

            Select::make('users')
            ->label('Service managers')
            ->preload()
            ->multiple()
            ->required()
            ->default(function () {
                $user = auth()->user()->hasRole(User::SERVICE_MANAGER) ? auth()->user()->id : null;
                return [$user];
            })
            ->relationship(
                name: 'users',
                // titleAttribute: 'full_name_email', // this doesn't work with mariadb
                titleAttribute: 'email',
                modifyQueryUsing: function(Builder $query){
                    return $query
                        ->addSelect([
                            'full_name_email' => DB::raw("CONCAT(name, ' ', surname, ' (', email, ')') as full_name_email"),
                            'users.id',
                            'users.name',
                            'users.surname',
                            'users.email'
                        ])
                        ->role(User::SERVICE_MANAGER)
                        ->orderBy('surname')
                        ->orderBy('name');
                }
            ),


            Repeater::make('research_references')
                ->columnSpan('full')
                ->schema([
                    TagsService::tagsSchemaForRepeater(
                        'reference_role_tag_field',
                        'reference_role',
                        'Reference type',
                        required: false,
                        multiple: false,
                        searchable: true,
                        preload: true
                    ),
                    TextInput::make('reference')
                        ->label('Citation'),
                    TextInput::make('url')
                        ->label('URL'),

                ])
                ->addActionLabel('Add research reference')
                ->columns()
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                    'Include various types of references here, specifying their purpose and what they relate to or provide further information about. The dropdown list offers examples, but you can add a custom entry by clicking on +. '
                    ),

            Repeater::make('external_pid')
                ->defaultItems(1)
                // ->minItems(1)
                ->columnSpan('full')
                ->schema([
                    TagsService::tagsSchemaForRepeater(
                        'pid_type_tag_field',
                        'persistent_identifier',
                        'Pid type',
                        required: false,
                        multiple: false,
                        searchable: true,
                        preload: true
                    ),
                    TextInput::make('pid')
                        ->label('PID')
                        ->columns(3)
                ])
                ->addActionLabel('Add external PID')
                ->columns()
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                    'Here you can include the unique organisations identifier. You can find the PID of your organisations in the registries ROR (https://ror.org/). You can also enter a different registry by clicking on +.'
                        ),
            Repeater::make('webpages')
                ->columnSpan('full')
                ->schema([
                    TextInput::make('url')
                        ->label('URL')
                ])->addActionLabel('Add webpage'),


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
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('acronym'),
                TextColumn::make('id')
                    ->label('Countries')
                    ->formatStateUsing(function ($state) {
                        return collect(Organization::find($state)->getCountries())->implode(', ');
                    })
                    ->limit(25)
                    ->tooltip(function ($state) {
                        return collect(Organization::find($state)->getCountries())->implode(', ');
                    }),
            ])
            ->filters([
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
                            // $query->whereHas('organization', function ($query) use ($data) {
                                $query->whereHas('tags', function ($query) use ($data) {
                                    $query->where('type', 'country')
                                        ->whereIn('name->en', array_map(function ($value) {
                                            return collect(json_decode($value, true))['en'];
                                        }, $data['values']));
                                });
                            // });
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
                    ->withFilename('Tools_' . date('Y-m-d'))
                    ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                    
                    ->fromForm()
                    ->ignoreFormatting([
                    ])
                    ->withColumns([
                        Column::make('users')
                        ->formatStateUsing(function ($state) {
                            $data = collect(json_decode($state, true));
                            return $data->map(function ($item) {
                                return $item['name'] . ' ' . $item['surname'] . ' (' . $item['email'] . ')';
                            })->implode("\r\n");
                        } ),
                        Column::make('research_references')
                        ->heading('Research references')
                                ->formatStateUsing(function ($state) {

                                    $result = [
                                        'reference_role_tag_field' =>[],
                                        'reference' => [$state['reference']],
                                        'url' => $state['url'],
                                        'blank_space' => [],
                                    ];
                                    if (isset($state['reference_role_tag_field'])) {
                                        foreach (collect(value: $state['reference_role_tag_field']) as $material_id) {
                                            $result['reference_role_tag_field'][] = Tag::find($material_id)->name;
                                        }
                                    }
                                    return $result;

                                    // re/sturn  Tag::find($state[''])->name . " - " . $state['url'];
                                }),
                        Column::make('external_pid')
                        ->heading('External PID')
                        ->formatStateUsing(function ($state) {
                            $result = [
                                'pid_type_tag_field' => [],
                                'pid' => $state['pid'],
                                'blank_space' => [],
                            ];
                            if (isset($state['pid_type_tag_field'])) {
                                foreach (collect(value: $state['pid_type_tag_field']) as $pid_id) {
                                    $result['pid_type_tag_field'][] = Tag::find($pid_id)->name;
                                }
                            }
                            return $result;
                        }),
                        Column::make('based_in')
                        ->heading('Based in')
                        ->getStateUsing(function($record) {
                            return $record->getCountries();
                        }),
                        Column::make('organization_type')
                        ->heading('Organization type')
                        ->getStateUsing(function($record) {
                            return $record->getOrganizationTypes();
                        }),


                        Column::make('research_disciplines')
                        ->heading('Fields of application')
                        ->getStateUsing(function($record) {
                            return $record->getResearchDisciplines();
                        }),
                    ])
                    ->modifyQueryUsing(fn(Builder $query, $livewire) => $query
                    ->whereIn('id', $livewire->selectedTableRecords)
                    ->with(['users', 'tags'])
                    ),
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
