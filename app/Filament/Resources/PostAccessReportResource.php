<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostAccessReportResource\Pages;

use App\Models\PostAccessReport;
use App\Models\Proposal;
use App\Models\ProposalService as ModelsProposalService;
use App\Services\PostAccessReportService;
use App\Services\ProposalService;
use App\Services\TagsService;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use Filament\Forms\Get;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class PostAccessReportResource extends Resource
{
    protected static ?string $model = PostAccessReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $slug = 'post-access-reports';
    protected static ?string $navigationGroup = 'My documents';


    public static function getEloquentQuery(): Builder
    {
        return PostAccessReportService::getMyPostAccessReportsQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('proposal_id')
                    ->label('Proposal')
                    ->options(

                        ProposalService::getMyClosedProposalQuery()->get()->pluck('name', 'id')
                    )
                    ->visibleOn('create')
                    ->required()
                    ->columnSpan(1)
                    ->live()
                    ->afterStateUpdated(fn ($get, $set) => $set('user_id', Proposal::find($get('proposal_id'))?->leader->first()->id)),
                Placeholder::make('proposal')
                    ->visibleOn(['edit', 'view'])
                    ->content(fn($get)=> Proposal::find($get('proposal_id'))?->name),
                Placeholder::make('spacer')
                    ->hidden(fn($get):bool =>$get('proposal_id') != null)
                    ->label(''),

                Placeholder::make('proposal_acronym')
                    ->hidden(fn($get):bool =>$get('proposal_id') == null)
                    ->content(function (Get $get) {
                        $proposalID = $get('proposal_id');
                        if ($proposalID) {
                            return Proposal::find($get('proposal_id'))->acronym;
                        }
                        return null;
                    }),
                Placeholder::make('services')
                    ->hidden(fn($get):bool =>$get('proposal_id') == null)
                    ->columnSpanFull()
                    ->content(
                        function (Get $get) {
                            $proposal = Proposal::find($get('proposal_id'));
                            if ($proposal == null){
                                return 'no proposal';
                            }
                            $list = '<ul>';
                            foreach ($proposal->services as $service) {

                                $proposalService = ModelsProposalService::where('proposal_id', $proposal->id)
                                    ->where('service_id', $service->id)->first();
                                    $list .= '<li><a target="_blank" href="' . route('service', ['id' => $service->id]) . '"><i>' . $service->title . '</i>' .
                                    '
                                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pb-1.5 size-5 w-5 h-5 inline-block">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                    '.

                                    ' </a> - Access date: <b>' . $proposalService->scheduled_date . '</b></li>';
                            }
                            $list .= '</ul>';
                            return new HtmlString($list);
                        }
                    ),
                /**@TODO mancano i seguenti campi:
                 * Research need automatically filled
                 * Service functions automatically filled
                 */
                TextInput::make('user_id')
                    ->hidden()
                    ->dehydratedWhenHidden(true)
                    ,


                    // ->relationship('user', 'name')
                    // ->required(function () {
                    //     $user = Auth::user();
                    //     return $user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE]);
                    // }
                    // )
                    // ->options(function (Get $get) {
                    //     $proposalID = $get('proposal_id');
                    //     if ($proposalID) {
                    //         return Proposal::find($get('proposal_id'))->leader->pluck('name','id');
                    //     }
                    //     return null;
                    // })

                    // ->visible(function () {
                    //     $user = Auth::user();
                    //     return $user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE]);
                    // })
                    // ->default(fn ()=> Auth::user()->id)
                    // ->dehydratedWhenHidden(),

                Textarea::make('summary')
                    ->label('Abstract')
                    ->columnSpanFull()
                    ->autosize()
                    ->hint(__('max 300 words'))
                    ->maxLength(65535)
                    ->required(),
                Textarea::make('core_description')
                    ->maxLength(65535)
                    ->autosize()
                    ->rows(8)
                    ->placeholder('
- Introduction and motivation for the visit
- Scientific objectives of the visit
- Reasons for choosing E-RIHS facilities
- Activity during your visit (please describe the steps taken, instrumentation used, techniques employed, data sources consulted, etc.)
- Method and set-up of the research
- Project achievements during the visit (and possible difficulties encountered)
- Preliminary project results and Conclusions
- Outcome and future studies
                    ') // this needs to be indented all to the left
                    ->columnSpanFull()
                    ->hint(__('max 3000 characters - 500 words'))
                    ->required(),
                Textarea::make('expected_publications')
                    ->label('Expected publications, presentations and other dissemination activities')
                    ->maxLength(65535)
                    ->autosize()
                    ->columnSpanFull(),
                TextInput::make('link')
                    ->maxLength(255),

                TagsService::tagsGrid(
                    name: 'keywords',
                    type: 'post_access_report_keywords',
                    label: 'Keywords',
                    required: true,
                    multiple: true,
                    hintIcon: 'heroicon-m-question-mark-circle',
                    hintTooltip: "Add a new item or use an existing one",
                    addable: true,
                ),


                Repeater::make('post_access_report_files')
                    ->relationship('files')
                    ->columnSpanFull()
                    ->addActionLabel('Add a new file')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('File')
                            ->downloadable()
                            ->previewable(false) // to show the actual file name in the form
                            ->required()
                            ->directory('post-access-duties-files')
                            ->visibility('private')
                            ->hintIcon(
                                'heroicon-m-question-mark-circle',
                                tooltip: 'Maximum file size: 50 Mb'
                            ),
                    ]),
                Repeater::make('post_access_report_photos')
                    ->relationship('photos')
                    ->columnSpanFull()
                    ->minItems(1)
                    ->addActionLabel('Add a new photo')
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Image')
                            ->downloadable()
                            ->previewable(false) // to show the actual file name in the form
                            ->directory('post-access-duties-images')
                            ->visibility('private')
                            ->image()
                            ->required()
                            ->hintIcon(
                                'heroicon-m-question-mark-circle',
                                tooltip: 'Maximum file size: 10 Mb'
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('id')->sortable(),
                TextColumn::make('proposal.name')->sortable()->searchable(),
                // TextColumn::make('core_description')->limit(50),
                TextColumn::make('created_at')->dateTime(),
                // TextColumn::make('updated_at')->dateTime(),
            ])

            ->recordAction(Tables\Actions\ViewAction::class)
            ->recordUrl(null)
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('Download PDF')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->label('Download PDF')
                    ->url(fn ($record) => route('post-access-report.pdf', ['id' => $record->id])),
                ViewAction::make()
                    ->extraModalFooterActions([
                        Action::make('downloadPdf')
                            ->label('Download PDF')
                            ->url(fn ($record) => route('post-access-report.pdf', ['id' => $record->id]))
                            ->openUrlInNewTab()
                    ]),
                DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected static function isEdit(): bool
    {
        // Recupera il record corrente
        $record = request()->route()->parameter('record');

        return $record !== null;
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPostAccessReports::route('/'),
            'create' => Pages\CreatePostAccessReport::route('/create'),
            'edit' => Pages\EditPostAccessReport::route('/{record}/edit'),
        ];
    }

    public static function sidebar(PostAccessReport $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->sidebarNavigation()
            ->setNavigationItems(MyProposalResource::navigationItems($record->proposal));
    }
}
