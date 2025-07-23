<?php

namespace App\Filament\Resources;


use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Organization;
use App\Models\Service;
use App\Models\User;
use App\Services\ServiceService;
use App\Services\TagsService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Spatie\Tags\Tag;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Settings';


    protected static ?int $navigationSort = 8;

    public static function getEloquentQuery(): Builder
    {
        if (
            \auth()->user()->hasPermissionTo('administer site') ||
            \auth()->user()->hasPermissionTo('administer services')
        ) {
            return parent::getEloquentQuery();
        }
        return ServiceService::getMyServicesQuery();
    }

    public static function formSchema()
    {

        return [

            Select::make('organization_id')
                ->disabled(
                    fn($livewire) =>
                    $livewire instanceof \Filament\Resources\Pages\EditRecord  &&
                        !auth()->user()->hasAnyRole([User::ADMIN_ROLE, User::HELP_DESK_ROLE])
                )
                ->relationship('organization', 'name')
                ->options(function () {
                    $user = auth()->user();
                    if (
                        auth()->user()->hasPermissionTo('administer site') ||
                        auth()->user()->hasPermissionTo('administer services')
                    ) {
                        return Organization::all()->pluck('name', 'id');
                    } else {
                        return $user->organizations()->pluck('name', 'organization_id');
                    }
                })
                ->live()
                ->afterStateUpdated(function ($get, $set) {
                    $managers = [];
                    foreach ($get('serviceManagers') as $managerId) {
                        $manager = User::find($managerId);
                        if ($manager && $manager->organizations()->where('organizations.id', $get('organization_id'))->exists()) {
                            $managers[] = $managerId;
                        }
                    }
                    $set('serviceManagers', $managers);
                })
                ->required(),
            TagsService::tagsGrid('provider_role', 'provider_role', 'Organization role', true, true, true),
            TextInput::make('title')
                ->maxLength(255)
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Service title. For an ARCHLAB service, this could be e.g. \'Access to KIK-IRPA Archives\', for the other platforms, this could be the name of the technique.')
                ->required(),

            Select::make('serviceManagers')
                ->label('Service managers')
                ->preload()
                ->required()
                ->minItems(1)
                ->multiple()
                ->live()
                ->default(function ($get) {
                    $user = auth()->user()->hasRole(User::SERVICE_MANAGER) ? auth()->user()->id : null;
                    return [$user];
                })
                ->relationship(
                    name: 'serviceManagers',
                    // titleAttribute: 'full_name_email', // this doesn't work with mariadb
                    titleAttribute: 'email',
                    modifyQueryUsing: function (Builder $query, $get) {
                        $organizationId = $get('organization_id');
                        return $query
                            ->addSelect([
                                'full_name_email' => DB::raw("CONCAT(name, ' ', surname, ' (', email, ')') as full_name_email"),
                                'users.id',
                                'users.name',
                                'users.surname',
                                'users.email'
                            ])
                            ->role(User::SERVICE_MANAGER)
                            ->whereHas('organizations', function (Builder $query) use ($organizationId) {
                                $query->where('organization_id', $organizationId);
                            })
                            ->orderBy('surname')
                            ->orderBy('name');
                    }
                ),

            Textarea::make('summary')
                ->columnSpanFull()
                ->autosize()
                ->maxLength(16777215)
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'A brief description of a specific technical or access service offered, such as the use of X to investigate Y')
                ->required(),

            TagsService::tagsGrid(
                name: 'readiness_level',
                type: 'readiness_level',
                label: 'Readiness level',
                required: true,
                multiple: false,
                searchable: true,
                hintIcon: 'heroicon-m-question-mark-circle',
                hintTooltip: 'It is the scale for estimating the maturity of a service. In case of ARCHLAB, we recommend to adopt Level 9'
            ),
            TagsService::tagsGrid(
                'operating_language',
                'operating_language',
                'Operating language',
                required: true,
                multiple: true,
                searchable: true,
                hintIcon: 'heroicon-m-question-mark-circle',
                hintTooltip: 'What languages can the team operate in or what language is a tool presented in'
            ),

            TextInput::make('version')
                ->maxLength(255)
                ->hintIcon(
                    icon: 'heroicon-m-question-mark-circle',
                    tooltip: 'Indicate the version of this Service'
                ),
            DatePicker::make('version_date'),
            TagsService::tagsGrid(
                name: 'access_unit',
                type: 'period_unit',
                label: 'Service access period unit',
                required: true,
                multiple: false,
                searchable: false,
                hintIcon: 'heroicon-m-question-mark-circle',
                hintTooltip: 'Insert here the unit used for the service expressed in days, hours, etc. The Service Access Period Unit is a measure specifying the access offered to the users, which may vary: e.g. precise values like hours or sessions of beam time processing time',
            ),
            TextInput::make('hours_per_unit')
                ->rules(['numeric'])
                ->label('Service Access Hours per Unit'),
            TextInput::make('access_unit_cost')
                ->rules(['numeric'])
                ->label('Service Access Unit Cost'),

            TagsService::tagsGrid(
                'technique',
                'technique',
                'Technique',
                required: false,
                multiple: true,
                searchable: true,
                hintIcon: 'heroicon-m-question-mark-circle',
                hintTooltip: 'A list of the individual relevant heritage science examination and analytical techniques carried out within this Service. If none of the techniques is applicable, use the \'Other service techniques\' field.'
            ),

            Textarea::make('other_techniques')
                ->label('Other service techniques')
                ->columnSpanFull(),

            TagsService::tagsGrid(
                'e-rihs_platform',
                'e-rihs_platform',
                'E-RIHS Platform',
                required: true,
                multiple: true,
                searchable: false
            ),

            TagsService::tagsGrid('research_disciplines', 'research_disciplines', 'Fields of application', true, true, true, 'heroicon-m-question-mark-circle', 'Which domains or disciplines has a given service worked within, has experience within. This field is connected to an extensive controlled list.'),


            TextArea::make('limitations')
                ->autosize()
                ->columnSpanFull()
                ->maxLength(16777215)
                ->hintIcon('heroicon-m-question-mark-circle', 'Description of any limitations in the access provision by the service.'),

            Textarea::make('description')
                ->autosize()
                ->columnSpanFull()
                ->maxLength(16777215)
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                    tooltip: 'A longer description/documentation of a specific technical or access service offered under E-RIHS. This description will be shown in the catalogue. Please be accurate and clear'
                )
                ->required(),
            FileUpload::make('picture')
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                    tooltip: 'Please upload a 440x286 pixels jpeg image'
                )
                ->downloadable()
                ->previewable(false) // to show the actual file name in the form
                ->image()
                ->columnSpanFull(),

            Textarea::make('output_description')
                ->autosize()
                ->maxLength(65535)
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                    tooltip: 'A description of all the raw and processed outputs created by the service including details of how they relate to or relay on each other.'
                )
                ->columnSpanFull(),
            Textarea::make('input_description')
                ->autosize()
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                    tooltip: 'A description of the required formats or files required by the service'
                )
                ->columnSpanFull(),
            Textarea::make('further_comments')
                ->autosize()
                ->columnSpanFull(),
            TextInput::make('slots')
                ->label('Access slot number (per year)')
                ->numeric()
                ->required(),
            Repeater::make('categories')
                ->label('Research Questions')
                ->columnSpanFull()
                ->addActionLabel('Add research question')
                ->required()
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                    tooltip: 'Add here the research questions that the service addresses. Example: Can we consult microscope images and analytical results on paint cross sections? How can I observe underdrawings? How can I identify the pigments in a painting?'
                )
                ->schema([
                    TextInput::make('category')
                        ->label('Research question')
                        ->required()
                ]),
            Repeater::make('functions')
                ->columnSpanFull()
                ->addActionLabel('Add function')
                ->required()
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Short and descriptive service practical level activities, what has a service been used for, what is it intended for. E.g. \'Materials Analysis\', \'Dye Analysis\', \'De-convolution XRF Spectra\', \'Calculating Light Exposure Allowance\'.')
                ->schema([
                    TextInput::make('function')
                        ->required()
                ]),
            Repeater::make('contacts')
                ->addActionLabel('Add contact')
                ->defaultItems(1)
                ->minItems(1)
                ->columnSpan('full')
                ->schema([
                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->columns(3),
                    TextInput::make('phone')
                        ->label('Phone number')
                        ->integer()
                        ->columns(3)
                ])
                ->label('Service contacts')
                ->columns(),
            Repeater::make('measurable_properties')
                ->addActionLabel('Add measurable property')
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Measurable properties and materials that can be studied by this service')
                ->columnSpan('full')
                ->schema([
                    TagsService::tagsSchemaForRepeater(
                        'class_tag_field',
                        'measurable_property',
                        'Measurable property',
                        required: false,
                        multiple: false,
                        searchable: true,
                        preload: true
                    ),

                    TagsService::tagsSchemaForRepeater(
                        'materials_tag_field',
                        'material',
                        'Materials',
                        required: true,
                        multiple: true,
                        searchable: true,
                        preload: true
                    ),
                    TextInput::make('materials_other')
                        ->label('Other materials')
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Other materials of which the selected measurable property can be studied (free text)')
                ])->columns(3),
            Repeater::make('links')
                ->addActionLabel('Add link')
                ->defaultItems(0)
                ->columnSpan('full')
                ->schema([
                    TagsService::tagsSchemaForRepeater(
                        'type_tag_field',
                        'link_type',
                        'Link type',
                        required: true,
                        multiple: false,
                        searchable: true,
                        preload: true
                    ),
                    TextInput::make('url')
                        ->required(),
                ]),
            Toggle::make('service_active')
                ->helperText(function($record) {
                    $text = '';
                    if ($record != null && !$record->service_active) {
                        $text .= 'This service is not active. It will not be visible in the catalogue.';
                        if (!auth()->user()->can('administer services')) {
                            $text .=  '<br/>To activate this service, please contact the help-desk at helpdesk@e-rihs.eu.';
                        }
                    } else {
                        $text .= 'This service is active. It will be visible in the catalogue.';
                    }


                    return new HtmlString($text);
                })
                ->disabled(function($record) {
                    if ($record == null || $record->service_active) {
                        return false;
                    }
                    if (auth()->user()->can('administer services')) {
                        return false;
                    }
                    return true;
                }),
            Toggle::make('application_required')
                ->label('Can be added to a proposal')
                ->default(true)
                ->live(),
            TextInput::make('url')
                ->maxLength(65535)
                ->label('Link to the service webpage')
                ->url()
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'It will be used in the catalogue to redirect the users to the service webpage')
                ->hidden(
                    function (\Filament\Forms\Get $get) {
                        if ($get('application_required'))
                            return true;
                    }
                )
                ->required(function () use (&$isHidden) {
                    return !$isHidden;
                })
                ->columnSpanFull()
                ->live(),

            Placeholder::make('Method and tool information')
                ->columnSpanFull()
                ->label('')
                ->content(new HtmlString(
                    '<div class="text-gray-500">
                        You will be able to insert Methods and Tools after you have created the service.
                    </div>'
                ))
                ->visibleOn('create'),
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
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->searchable(),
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

                TextColumn::make('e-rihs_platform')
                    ->label('Platform')
                    ->getStateUsing(function ($record) {
                        return $record->getPlatforms();
                    })
                    ->badge(),

                TextColumn::make('service_active')
                    ->formatStateUsing(function ($state) {
                        return $state ?     'Yes' : 'No';
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

                            $data = Tag::where('type', 'country')
                                ->whereIn('id', function ($query) {
                                    $query->select('tag_id')
                                        ->from('taggables')
                                        ->join('tags', 'tags.id', '=', 'taggables.tag_id')
                                        ->where('taggable_type', Organization::class)
                                        ->orderBy('tags.name')
                                    ;
                                })
                                ->pluck('name', 'name')
                                ->toArray();
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
                    ->multiple(),

                    Tables\Filters\SelectFilter::make('platform')
                    ->label('Platform')
                    ->options(
                        function () {

                            $data = Tag::where('type', 'e-rihs_platform')
                                ->whereIn('id', function ($query) {
                                    $query->select('tag_id')
                                        ->from('taggables')
                                        ->join('tags', 'tags.id', '=', 'taggables.tag_id')
                                        ->where('taggable_type', Service::class)
                                        ->orderBy('tags.name')
                                    ;
                                })
                                ->pluck('name', 'name')
                                ->toArray();
                            asort($data);
                            return $data;
                        }
                    )
                    ->query(function ($query, $data) {
                        if (!empty($data['values'])) {
                                $query->whereHas('tags', function ($query) use ($data) {
                                    $query->where('type', 'e-rihs_platform')
                                        ->whereIn('name->en', array_map(function ($value) {
                                            return collect(json_decode($value, true))['en'];
                                        }, $data['values']));
                            });
                        }
                    })
                    ->multiple(),

                


                Tables\Filters\SelectFilter::make('service_active')
                    ->label('Service active')
                    ->options([
                        true => 'Yes',
                        false => 'No',
                    ]),

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
                            ->withFilename('Services_' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            // ->withColumns([
                            // ])
                            // ->fromForm()
                            // ->except([
                            //     'picture',
                            //     'created_at',
                            //     'updated_at',
                            // ])
                            ->withColumns([
                                // Column::make('id')
                                //     ->heading('URL address')
                                //     ->formatStateUsing(function ($state) {
                                //         return env('APP_URL') . "/dashboard/services/". $state ."/edit";
                                //     }),

                                Column::make('organization_id')
                                    ->heading('Organization')
                                    ->formatStateUsing(function ($state) {
                                        return  Organization::find($state)->name;
                                    }),

                                Column::make('title'),
                                Column::make('summary'),

                                Column::make('serviceManagers')
                                    ->formatStateUsing(function ($state) {
                                        $data = collect(json_decode($state, true));
                                        return $data->map(function ($item) {
                                            return $item['name'] . ' ' . $item['surname'] . ' (' . $item['email'] . ')';
                                        })->implode("\r\n");
                                    }),


                                Column::make('provider_role')
                                    ->heading('Organization role')
                                    ->getStateUsing(function ($record) {
                                        return $record->getProviderRoles();
                                    })
                                    ->formatStateUsing(function ($state) {
                                        return $state;
                                    }),
                                Column::make('readiness_level')
                                    ->heading('Readiness level')
                                    ->getStateUsing(function ($record) {
                                        return $record->getReadinessLevels();
                                    })
                                    ->formatStateUsing(function ($state) {
                                        return $state;
                                    }),


                                Column::make('operating_language')
                                    ->heading('Operating language')
                                    ->getStateUsing(function ($record) {
                                        return $record->getOperatingLanguages();
                                    })
                                    ->formatStateUsing(function ($state) {
                                        return $state;
                                    }),


                                Column::make('version'),
                                Column::make('version_date'),


                                Column::make('access_unit')
                                    ->heading('Service access period unit')
                                    ->getStateUsing(function ($record) {
                                        return $record->getPeriodUnit();
                                    })
                                    ->formatStateUsing(function ($state) {
                                        return $state;
                                    }),

                                Column::make('hours_per_unit'),
                                Column::make('access_unit_cost'),

                                Column::make('technique')
                                    ->heading('Technique')
                                    ->getStateUsing(function ($record) {
                                        return $record->getTechniques();
                                    })
                                    ->formatStateUsing(function ($state) {
                                        return $state;
                                    }),

                                Column::make('e-rihs_platform')
                                    ->heading('E-RIHS Platform')
                                    ->getStateUsing(function ($record) {
                                        return $record->getPlatforms();
                                    })
                                    ->formatStateUsing(function ($state) {
                                        return $state->implode(', ');
                                    }),

                                Column::make('research_disciplines')
                                    ->heading('Fields of application')
                                    ->getStateUsing(function ($record) {
                                        return $record->getResearchDisciplines();
                                    })
                                    ->formatStateUsing(function ($state) {
                                        return $state;
                                    }),

                                Column::make('limitations'),
                                Column::make('description'),
                                Column::make('output_description'),
                                Column::make('input_description'),
                                Column::make('further_comments'),
                                Column::make('slots'),
                                Column::make('categories'),
                                Column::make('functions'),





                                Column::make(name: 'contacts')
                                    ->heading('Service contacts')
                                    ->formatStateUsing(function ($state) {
                                        return "Email: " . $state['email'] . " - Phone: " . $state['phone'];
                                    }),


                                Column::make('measurable_properties')
                                    ->heading('Measurable properties')
                                    ->formatStateUsing(function ($state) {
                                        $result = [
                                            'measurable_property' => [],
                                            'materials' => [],
                                            'other_materials' => $state['materials_other'],
                                            'blank_space' => [],
                                        ];
                                        if (isset($state['materials_tag_field'])) {
                                            foreach (collect($state['materials_tag_field']) as $material_id) {
                                                $result['materials'][] = Tag::find($material_id)->name;
                                            }
                                        }
                                        if (isset($state['class_tag_field'])) {
                                            foreach (collect($state['class_tag_field']) as $property_id) {
                                                $result['measurable_property'][] = Tag::find($property_id)->name;
                                            }
                                        }
                                        return $result;
                                    }),

                                Column::make('links')
                                    ->heading('Links')
                                    ->formatStateUsing(function ($state) {
                                        return Tag::find($state['type_tag_field'])->name . " - " . $state['url'];
                                    }),
                                Column::make('based_in')
                                    ->heading('Countries')
                                    ->getStateUsing(function ($record) {
                                        return $record->organization->getCountries();
                                    }),


                                Column::make('service_active')
                                    ->formatStateUsing(function ($state) {
                                        return $state ? 'Yes' : 'No';
                                    }),

                                Column::make('application_required')
                                    ->formatStateUsing(function ($state) {
                                        return $state ? 'Yes' : 'No';
                                    }),



                            ])
                            ->ignoreFormatting([])
                            ->modifyQueryUsing(fn(Builder $query, $livewire) => $query
                                ->whereIn('id', $livewire->selectedTableRecords)
                                ->with(['organization', 'serviceManagers']))
                    ])
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\OrganizationRelationManager::class,
            // RelationManagers\MethodRelationManager::class,
            // RelationManagers\ToolRelationManager::class,
            RelationManagers\MethodServiceToolRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
