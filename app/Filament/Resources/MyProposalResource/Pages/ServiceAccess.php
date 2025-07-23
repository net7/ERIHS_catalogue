<?php

namespace App\Filament\Resources\MyProposalResource\Pages;

use App\Enums\ProposalStatus;
use App\Filament\Resources\MyProposalResource;
use App\Models\ProposalService;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class ServiceAccess extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithRecord;
    use HasPageSidebar;

    protected static string $resource = MyProposalResource::class;

    protected static string $view = 'filament.resources.proposal-resource.pages.list-proposal-services';

    public function table(Table $table): Table
    {
        $record = $this->record;

        $user = Auth::user();

        return $table
            ->relationship(
                fn(): BelongsToMany => env('IS_TEST', false) ?
                    $record->services() :
                    $record->servicesByServiceManager($user->id)
            )
            ->columns([
                // TextColumn::make('service_id'),
                TextColumn::make('title')
                    ->label(__('Service name')),
                TextColumn::make('accessStatus')
                    ->label('Access status')
                    ->getStateUsing(function ($record) {
                        $proposalService = $this->record->proposalServiceOfService($record->service_id);
                        $access = $proposalService->access;
                        switch ($access) {
                            case ProposalService::ACCESS_SCHEDULED:
                                return "Access scheduled";
                            case ProposalService::ACCESS_CARRIED_OUT:
                                return "Access carried out";
                            default:
                                return "Not scheduled yet";
                        }
                    })
            ])
            ->actions([
                Action::make('scheduleAccess')
                    ->label('Schedule access')
                    ->visible(function ($record) {
                        $proposalService = $this->record->proposalServiceOfService($record->service_id);
                        return ($this->record->status == ProposalStatus::FILES_CONFIRMED->value) && is_null($proposalService->access);
                    })
                    ->form([
                        DatePicker::make('scheduled_date')->required(),
                    ])
                    ->action(function (array $data, $record) {
                        $proposalService = $this->record->proposalServiceOfService($record->service_id);
                        $proposalService->access = ProposalService::ACCESS_SCHEDULED;
                        $proposalService->scheduled_date = $data['scheduled_date'];
                        $proposalService->save();
                        Notification::make()
                            ->title('Success')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Please enter the date scheduled for the access to the service'),
                Action::make('carriedOutAccess')
                    ->label('Carry out access')
                    ->visible(function ($record) {
                        $proposalService = $this->record->proposalServiceOfService($record->service_id);
                        return ($proposalService->access == ProposalService::ACCESS_SCHEDULED);
                    })
                    ->requiresConfirmation()
                    ->modalDescription(description: 'Do you confirm that the access to the service has been carried out completely?')

                    ->action(function ($record) {
                        $proposalService = $this->record->proposalServiceOfService($record->service_id);
                        $proposalService->access = ProposalService::ACCESS_CARRIED_OUT;
                        $proposalService->save();
                        Notification::make()
                            ->title('Success')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}
