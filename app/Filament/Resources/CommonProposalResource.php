<?php

namespace App\Filament\Resources;

use App\Models\Proposal;
use App\Models\ProposalEvaluation;
use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class CommonProposalResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static int $globalSearchResultsLimit = 20;

    public static function navigationItems(Proposal $record): array
    {

        return [
            PageNavigationItem::make('General information')
                ->url(function () use ($record) {
                    return static::getUrl('general-info', ['record' => $record->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.proposals.general-info';
                }),
            PageNavigationItem::make('Application history')
                ->url(function () use ($record) {
                    return static::getUrl('application-history', ['record' => $record->id]);
                })->icon('heroicon-o-rectangle-stack')
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.proposals.application-history';
                }),
        ];
    }

    public static function sidebar(Proposal $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->sidebarNavigation()
            ->setNavigationItems(static::navigationItems($record));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        return $table
            ->contentGrid([
                'xl' => 1,
                '2xl' => 1,
            ])
            ->columns([
                ViewColumn::make('ribbon')->view('tables.ribbon'),
                View::make('tables.columns.proposal-card'),
                TextColumn::make('name')->sortable()->searchable()->extraAttributes(['class' => 'hidden']),
                TextColumn::make('acronym')->sortable()->searchable()
                    ->getStateUsing(function (Proposal $record): string {
                        return 'Acronym: ' . $record->acronym;
                    }),
                TextColumn::make('type')
                    ->getStateUsing(function (Proposal $record): string {
                        return 'Type: ' . $record->type;
                    }),
                TextColumn::make('status')
                    ->getStateUsing(function (Proposal $record): string {
                        return 'Status: ' . $record->getLastStatusDescription();
                    }),
                TextColumn::make('weighted_average')->sortable()
                    ->getStateUsing(function (Proposal $record): string {
                        $weighted_average = ProposalEvaluation::calculateAverages($record->id);
                        if ($weighted_average == 0) {
                            return 'Weighted average: Not evaluated yet';
                        }
                        return 'Weighted average: ' . $weighted_average;
                    })->hidden(!$user->hasAnyRole([User::HELP_DESK_ROLE, User::ADMIN_ROLE])),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(function ($record) use ($user) {

                        if (
                            $record->isInDraftState() &&
                            (
                                $record->leader()->first() == null ||
                                !$record->isUserLeader($user))
                        ) {
                            return true;
                        }
                    })
                    ->label(
                        fn($record) => $record->isInFirstDraft() ? __('Complete proposal') : __('Manage')
                    )
                    ->url(function ($record): string {
                        if ($record->isInFirstDraft()) {
                            return '/proposal';
                        } else {
                            return static::$slug . '/' . $record->id . '/general-info';
                        }
                    })
                    ->icon('heroicon-s-cog')
                    ->button(),

                Tables\Actions\Action::make('acceptProposal')
                    ->label('Accept Proposal')
                    ->requiresConfirmation()
                    ->visible(function ($record) {
                        return $record->canBeAcceptedBy();
                    })
                    ->modalDescription("After accepting the proposal, all the service managers will contact you for scheduling the proposal access.")
                    ->icon('heroicon-s-check')
                    ->button()
                    ->action(function ($record) {
                        $record->accept();
                    }),
            ])
            ->bulkActions([
                ExportBulkAction::make('export')
                    ->exports([
                        ExcelExport::make()
                            ->withFilename('MyApplications_' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->withColumns([
                                Column::make('name'),
                                Column::make('acronym'),
                                Column::make('type'),
                                Column::make('applicants')
                                    ->getStateUsing(function ($record) {
                                        return $record->applicants;
                                    })
                                    ->formatStateUsing(function ($state) {
                                        $data = collect(json_decode($state, true));
                                        return $data->map(function ($item) {
                                            return $item['name'] . ' ' . $item['surname'] . ' (' . $item['email'] . ')' .
                                                ($item['pivot']['alias'] ? ' (alias)' : '') .
                                                ($item['pivot']['leader'] ? ' (leader)' : '');
                                        })->implode("\r\n");
                                    }),
                                Column::make('services')
                                    ->getStateUsing(function ($record) {
                                        return $record->proposalServices;
                                    })
                                    ->formatStateUsing(function ($state) {
                                        return $state->map(function ($item) {
                                            return
                                                //"<a href=\"" . 
                                                // ServiceResource::getUrl('edit', ['record' => $item->service->id]) . 
                                                // "\">" .
                                                $item->service->id . ' - ' . $item->service->title . ' (' .  $item->service->getPlatforms()->implode(', ') . ')'
                                                // . "</a>"
                                            ;
                                        })->implode("\r\n");
                                    }),
                                Column::make('call')
                                    ->getStateUsing(function ($record) {
                                        return $record->call;
                                    })
                                    ->formatStateUsing(function ($state) {
                                        return $state->label;
                                    }),
                                Column::make('research_disciplines')
                                    ->getStateUsing(function ($record) {
                                        return $record->getResearchDisciplines();
                                    }),
                                Column::make('status')
                                    ->getStateUsing(function ($record) {
                                        return $record->status;
                                    }),
                                Column::make('weighted_average')
                                    ->getStateUsing(function (Proposal $record): string {
                                        $weighted_average = ProposalEvaluation::calculateAverages($record->id);
                                        if ($weighted_average == 0) {
                                            return 'Not evaluated yet';
                                        }
                                        return $weighted_average;
                                    }),
                            ])
                            ->modifyQueryUsing(fn(Builder $query, $livewire) => $query->whereIn('id', $livewire->selectedTableRecords))
                    ])
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
            ]);
    }
}
