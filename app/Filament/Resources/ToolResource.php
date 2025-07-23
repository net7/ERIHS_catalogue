<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ToolResource\Pages;
use App\Models\Organization;
use App\Models\User;
use App\Services\TagsService;
use App\Services\ToolService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Spatie\Tags\Tag;

class ToolResource extends Resource
{

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 6;

    public static function getEloquentQuery(): Builder
    {
        if (\auth()->user()->hasPermissionTo('administer site') || 
            \auth()->user()->hasPermissionTo('administer tools')) {
            return parent::getEloquentQuery();
        }
        return ToolService::getMyTools();
    }


    public static function formSchema()
    {

        return [
            Radio::make('tool_type')
                ->label('Tool type')
                // ->columns()
                ->columnSpanFull()
                ->options([
                    'equipment' => 'Equipment',
                    'software' => 'Software',
                ])
                ->default('equipment')
                ->live(),
            TextInput::make('name')
                ->minLength(3)
                ->maxLength(255)
                ->required(),
            Select::make('organization_id')
                ->disabled(fn ($livewire) =>
                    $livewire instanceof \Filament\Resources\Pages\EditRecord  && 
                    !auth()->user()->hasAnyRole([User::ADMIN_ROLE, User::HELP_DESK_ROLE]) 
                    )
                ->relationship('organization', 'name')
                //->createOptionForm(auth()->user()->hasPermissionTo('administer site') || auth()->user()->hasPermissionTo('administer tools')? OrganizationResource::formSchema() : null)
                //->editOptionForm(OrganizationResource::formSchema())
                ->options(function () {
                    $user = auth()->user();
                    if (auth()->user()->hasPermissionTo('administer site') ||
                        auth()->user()->hasPermissionTo('administer tools')) {
                        return Organization::all()->pluck('name', 'id');
                    } else {
                        return $user->organizations()->pluck('name', 'organization_id');
                    }
                })
                ->required(),
            Textarea::make('description')
                ->maxLength(16777215)
                ->required()
                ->columnSpan('full')
                ->required()
                ->autosize(),


            TagsService::tagsGrid(
                name: 'output_data_types',
                type: 'tool_output_data_types',
                label: 'Output data types',
                required: true,
                multiple: true,
                hintIcon: 'heroicon-m-question-mark-circle',
                hintTooltip: "Add a new item or use an existing one",
                addable: true,
            ),

            TagsService::tagsGrid('licence_type', 'licence_type', 'License Type', required: true, multiple: false)
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'equipment'),


            TagsService::tagsGrid(
                    name: 'acquisition_areas',
                    type: 'tool_equipment_acquisition_areas',
                    label: 'Acquisition Areas',
                    required: true,
                    multiple: true,
                    hintIcon: 'heroicon-m-question-mark-circle',
                    hintTooltip: "The areas of the object or sample that can be examined by the equipment. This dropdown list gives a few examples, but if required, you can enter a different term here depending on the equipment. One general recommendation is to extend the existing term with a precise value or a range (e.g. 'small spot (27mm2)'). ",
                    addable: true,
            ),


            TagsService::tagsGrid(
                name: 'input_data_types',
                type: 'tool_software_input_data_types',
                label: 'Input Data types',
                required: true,
                multiple: false
            )
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'equipment'),
            DatePicker::make('last_checked_date')
                ->required()
                ->hintIcon(
                    'heroicon-m-question-mark-circle',
                    'For hardware tools the last checked date relates to when equipment was last tested or callibrated, to confirm that it is working and performing as designed'
                ),
            DatePicker::make('calibration')
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'software')
                ->live()
                ->label('Last calibration date')
                ->hintIcon('heroicon-m-question-mark-circle', 'Date when the equipment was last calibrated.'),
            DatePicker::make('release_date')
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'equipment')
                ->live()
                ->label('Release date')
                ->hintIcon('heroicon-m-question-mark-circle', 'When was the software tool first released or developed.'),
            TextInput::make('manufacturer')
                ->maxLength(255)
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'software')
                ->live(),
            TextInput::make('developer')
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'equipment')
                ->live(),
            TextInput::make('model')
                ->maxLength(255)
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'software')
                ->live(),
            TextInput::make('version')
                ->label('Software version')
                ->maxLength(255)
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'equipment')
                ->live(),
            TextInput::make('serial_number')
                ->maxLength(255)
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'software')
                ->live(),
            TagsService::tagsGrid('object_impact', 'object_impact', 'Impact on object or sample', required: false, multiple: true, searchable: false)
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'software'),
            TagsService::tagsGrid(
                name: 'working_distances',
                type:  'working_distance',
                label: 'Working distance',
                required: false,
                alphabetical: false)
                ->hidden(fn(\Filament\Forms\Get $get): bool => $get('tool_type') == 'software'),
            Repeater::make('url')
                ->label('Link')
                ->columnSpan('full')
                ->schema(
                    [
                        TagsService::tagsSchemaForRepeater(
                            'link_type_tag_field',
                            'link_type',
                            'Type',
                            required: true,
                            multiple: false,
                            searchable: true,
                            preload: true
                        ),
                        TextInput::make('url')
                            ->required()
                            ->label('Link')
                            ->columns(3)
                    ]
                )->addActionLabel('Add Link')->columns(),
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
                    ->tooltip(fn($record): string => $record->name)
                    ->limit(50)
                    ->searchable(),
                // TextColumn::make('description')
                    // ->tooltip(fn($record): string => $record->description)
                    // ->limit(50),
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
                        ->withFilename('Tools_' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                        ->only([
                            'tool_type',
                            'name',
                            'description',
                            'organization_id',
                            'last_checked_date',
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


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTools::route('/'),
            'create' => Pages\CreateTool::route('/create'),
            'edit' => Pages\EditTool::route('/{record}/edit'),
        ];
    }
}
