<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use IbrahimBougaoua\FilamentRatingStar\Actions\RatingStar;

class ProposalRating extends EditRecord //extends EditRecord
{
    use HasPageSidebar;

    protected static string $resource = ProposalResource::class;

    // protected static string $view = 'filament.resources.proposal-resource.pages.rating';

    protected static ?string $breadcrumb = "Rating";
    protected static ?string $title = "Rating";

  

    // public function mount($record){
    //     parent::mount($record);
    // }
    //   public function mount(): void
    // {
    //     $this->record = $this->resolveRecord($record);

    //     $this->authorizeAccess();

    //     $this->fillForm();

    // }

    // public function formPageRegistration(Form $form): Form
    public function form(Form $form): Form
    {
        $self = $this;
        return $form
            ->schema([
                Section::make()->columns(5)->schema([
                    RatingStar::make('rating')
                        ->label('Rating')
                        ->live()
                        // ->afterStateUpdated(
                        //     function (\Filament\Forms\Set $set, $state) use ($self) {
                        //         $self->save(false);
                        //     }
                        // )
                ])
            ])

            ;
    }
}
