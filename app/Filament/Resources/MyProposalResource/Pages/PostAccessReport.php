<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Enums\ProposalStatus;
use App\Filament\Resources\CommonProposalResource\Pages\PostAccessReport as PagesPostAccessReport;
use App\Filament\Resources\MyProposalResource;
use App\Filament\Resources\PostAccessReportResource;
use App\Models\PostAccessReport as ModelsPostAccessReport;
use App\Models\Proposal;
use App\Models\ProposalService;
use App\Models\User;
use App\Services\PostAccessReportService;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class PostAccessReport extends PagesPostAccessReport
{
    use HasPageSidebar;

    protected static string $resource = PostAccessReportResource::class;

    protected static string $view = 'filament.resources.proposal-resource.pages.post-access-reports';

    // public function form(Form $form): Form
    // {
    //     $record = $this->record;
    //     return $form
    //         ->schema([
    //             Select::make('proposal_id')
    //                 ->relationship('proposal', 'name')
    //                 ->options(
    //                     function () {
    //                         return Proposal::all()->pluck('name', 'id');
    //                     }
    //                 )
    //                 ->visible(function () {
    //                     $user = Auth::user();
    //                     return $user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE]);
    //                 })
    //                 ->required(),
    //             Placeholder::make('proposal_name')
    //                 ->content(fn(ModelsPostAccessReport $record): string => $record->proposal->name)
    //                 ->visible(function () {
    //                     $user = Auth::user();
    //                     return !$user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE]);
    //                 })
    //                 ->content(fn(ModelsPostAccessReport $record): string => $record->proposal->acronym),
    //             Placeholder::make('proposal_acronym')
    //                 ->visible(fn(?ModelsPostAccessReport $record) => $record !== null)
    //                 ->content(fn(ModelsPostAccessReport $record): string => $record->proposal->acronym),
    //             Placeholder::make('tools')
    //                 ->visible(fn(?ModelsPostAccessReport $record) => $record !== null)
    //                 ->content(
    //                     function (ModelsPostAccessReport $record) {
    //                         $list = '<ul>';
    //                         foreach ($record->proposal->services as $service) {
    //                             foreach ($service->methodServiceTool as $serviceTool) {
    //                                 $list .= '<li>' . $serviceTool->tool->name . '</li>';
    //                             }
    //                         }
    //                         $list .= '</ul>';
    //                         return new HtmlString($list);
    //                     }
    //                 ),
    //             /**@TODO mancano i seguenti campi:
    //              * Research need automatically filled
    //              * Service functions automatically filled
    //              */
    //             Select::make('user_id')
    //                 ->relationship('user', 'name')
    //                 ->visible(function () {
    //                     $user = Auth::user();
    //                     return $user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE]);
    //                 })
    //                 ->dehydratedWhenHidden(true)
    //                 ->required(),
    //             Placeholder::make('user_id')
    //                 ->content(fn(ModelsPostAccessReport $record): string => $record->user->name)
    //                 ->visible(function () {
    //                     $user = Auth::user();
    //                     return !$user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE]);
    //                 }),
    //             Textarea::make('summary')
    //                 ->label('Abstract')
    //             ->required(),
    //             Textarea::make('core_description')
    //                 ->required(),
    //             Textarea::make('expected_publications')
    //                 ->required(),
    //             TextInput::make('link')->required(),
    //             SpatieTagsInput::make('keywords')->type('post_access_report_keywords'),
    //             Repeater::make('post_access_report_files')
    //                 ->relationship('files')
    //                 ->schema([
    //                     FileUpload::make('file_path')
    //                         ->label('File')
    //                         ->downloadable()
    //                         ->previewable(false) // to show the actual file name in the form
    //                         ->required()
    //                         ->directory('post-access-duties-files')
    //                         ->visibility('private')
    //                         ->hintIcon(
    //                             'heroicon-m-question-mark-circle',
    //                             tooltip: 'Maximum file size: 50 Mb'
    //                         )
    //                 ]),
    //             Repeater::make('post_access_report_photos')
    //                 ->relationship('photos')
    //                 ->schema([
    //                     FileUpload::make('image_path')
    //                         ->label('Image')
    //                         ->downloadable()
    //                         ->previewable(false) // to show the actual file name in the form
    //                         ->directory('post-access-duties-images')
    //                         ->visibility('private')
    //                         ->image()
    //                         ->required()
    //                         ->hintIcon(
    //                             'heroicon-m-question-mark-circle',
    //                             tooltip: 'Maximum file size: 50 Mb'
    //                         ),
    //                 ]),
    //         ]);
    // }
}
