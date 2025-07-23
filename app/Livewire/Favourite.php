<?php

namespace App\Livewire;


use App\Models\Service;
use App\Services\ERIHSFavouriteService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;


class Favourite extends Component implements HasForms, HasInfolists, HasTable
{
    use InteractsWithForms;
    use InteractsWithInfolists;
    use InteractsWithTable;

    public $services;

    protected $listeners = ['refreshFavourites' => 'resetTable'];

    public function mount()
    {
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Service::whereIn('id', ERIHSFavouriteService::getItemsIds()))
            ->columns([
                    TextColumn::make('name')
                        ->searchable()
                        ->extraAttributes(['class' => 'hidden']),

                    View::make('livewire.favourite-servicebox'),
                ]
            )
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->contentGrid(['sm' => 1])
            ->emptyStateIcon('heroicon-o-bookmark')
            ->emptyStateHeading('It looks like your list is empty')
            ->emptyStateDescription('Start saving items to keep track of your favorites.')
            ->paginated();
    }

    public function render()
    {
        return view('livewire.favourite')->layout('components.layouts.app');
    }
}
