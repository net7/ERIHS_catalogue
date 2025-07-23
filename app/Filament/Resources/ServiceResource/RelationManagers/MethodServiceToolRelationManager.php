<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use App\Filament\Resources\MethodResource;
use App\Filament\Resources\ToolResource;
use App\Models\Method;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Tool;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;

class MethodServiceToolRelationManager extends RelationManager
{
    protected static string $relationship = 'methodServiceTool';

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }

    public function getOrganizationId()
    {
        $service_id = $this->getOwnerRecord()->id;
        $organization_id = Service::find($service_id)->organization_id;
        return $organization_id;
    }

    public function getTools()
    {
        return Tool::where('organization_id', $this->getOrganizationId())->get()->pluck('name', 'id');
    }

    public function getMethods()
    {
        return Method::where('organization_id', $this->getOrganizationId())->get()->pluck('preferred_label', 'id');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('method_id')
                    ->preload()
                    ->searchable()
                    ->relationship('method', 'preferred_label')
                    ->options(fn() => $this->getMethods())
                    ->createOptionForm(MethodResource::formSchema())
                    ->createOptionModalHeading('Create Method')
                    ->editOptionForm(MethodResource::formSchema())
                    ->editOptionModalHeading(function ($state) {
                        return 'Edit method ' . Method::find($state)->preferred_label;
                    })
                    ->columnSpanFull()
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, callable $get, RelationManager $livewire) {
                        return $rule
                            ->where('method_id', $get('method_id')) // get the current value in the 'school_id' field
                            ->where('tool_id', $get('tool_id'))
                            ->where('service_id', $livewire->getOwnerRecord()?->id);
                    })
                    ->validationMessages([
                        'unique' => 'The combination of method and tool is already present.',
                    ]),

                Placeholder::make('method data')
                    ->columnSpanFull()
                    ->content(function (callable $get, RelationManager $livewire) {
                        $method = Method::find($get('method_id'));
                        if ($method) {
                            if ($method->method_type) {
                                echo "<strong>Type:</strong> " . $method->method_type . "<br>";
                            }
                            if ($method->method_documentation) {
                                echo '<strong>Documentation:</strong> ' . $method->method_documentation . "<br>";
                            }
                            if ($method->method_description) {
                                echo '<strong>Description:</strong> ' . $method->method_description . "<br>";
                            }
                        }
                    })
                    ->hidden(function ($get, RelationManager $livewire): bool {
                        $method = Method::find($get('method_id'));
                        if (!$method &&
                            (
                                $livewire->mountedTableActions[0] == 'view' ||
                                $livewire->mountedTableActions[0] == 'create'

                            )
                        ) {
                            return true;
                        }
                        return false;
                    })
                    ,

                Select::make('tool_id')
                    ->preload()
                    ->searchable()
                    ->label('Tool')
                    ->relationship('tool', 'name')
                    ->options(fn() => $this->getTools())
                    ->createOptionForm(ToolResource::formSchema($this->getOrganizationId()))
                    ->createOptionModalHeading('Create Tool')
                    ->editOptionForm(ToolResource::formSchema())
                    ->editOptionModalHeading(function ($state) {
                        return 'Edit tool ' . Tool::find($state)->name;
                    })
                    ->columnSpanFull()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, callable $get, RelationManager $livewire) {
                        return $rule
                            ->where('method_id', $get('method_id')) // get the current value in the 'school_id' field
                            ->where('tool_id', $get('tool_id'))
                            ->where('service_id', $livewire->getOwnerRecord()?->id);
                    })
                    ->validationMessages([
                        'unique' => 'The combination of method and tool is already present.',
                    ])
                    ->hidden(function ($get, RelationManager $livewire): bool {
                        $tool = Tool::find($get('tool_id'));
                        if ($livewire->mountedTableActions[0] == 'view' && !$tool) {
                            return true;
                        }
                        return false;

                    })
                    ,
                Placeholder::make('tool data')
                    ->columnSpanFull()
                    ->content(function (callable $get, RelationManager $livewire) {
                        $tool = Tool::find($get('tool_id'));
                        if ($tool) {
                            echo '<strong>Description:</strong> ' . $tool->description . "<br>";
                            $organization = Organization::find($tool->organization_id);
                            if ($organization) {
                                echo '<strong>Organization:</strong> ' . $organization->name . "<br>";
                            }
                        }
                    })
                    ->hidden(function ($get, RelationManager $livewire): bool {
                        $tool = Tool::find($get('tool_id'));
                        if (!$tool &&
                            (
                                $livewire->mountedTableActions[0] == 'view' ||
                                $livewire->mountedTableActions[0] == 'create'

                            )
                        ) {
                            return true;
                        }
                        return false;

                    })
                   
                ,
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle('Method and Tool')
            ->columns([
                Tables\Columns\TextColumn::make('method.preferred_label')
                    ->tooltip(fn($record): string => $record->method ? $record->method->preferred_label : '')
                    ->limit(50),
                Tables\Columns\TextColumn::make('tool.name')
                    ->tooltip(fn($record): string => $record->tool ? $record->tool->name : '')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Add a method and a tool'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
