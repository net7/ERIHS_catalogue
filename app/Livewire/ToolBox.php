<?php

namespace App\Livewire;

use App\Services\ERIHSAuthService;
use App\Services\ERIHSCartService;
use App\Services\ERIHSFavouriteService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ToolBox extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    //TODO: Questa classe può essere eliminata?

    public $tool;

    public $showFavouritesInteractionButtons = true;

    public $showCartInteractionButtons = true;

    public $hideOuterBorder = false;

    public $showViewDetailsButton = false;

    public $removeButtonText = "Remove from proposal";

    // TODO: remove param and assignement?
    // public function mount($tool): void
    // {
    //     $this->tool = $tool;
    // }

    public function addToCart(): void
    {
        if ($this->tool) {
            ERIHSCartService::addItem($this->tool->id);

            $this->dispatch('refreshCart');

            Notification::make()
                ->title('Tool "'.$this->tool->name.'" added to proposal')
                ->success()
                ->send();

        }
    }

    public function removeFromCart(): void
    {
        if ($this->tool) {
            ERIHSCartService::removeItem($this->tool->id);

            $this->dispatch('refreshCart');

            Notification::make()
                ->title('Tool "'.$this->tool->name.'" removed from proposal')
                ->success()
                ->send();
        }
    }

    public function addToCartAction(): Action
    {
        $self = $this;

        return Action::make('addToCart')
            ->action(function () use ($self) {
                $self->addToCart();
            })
            ->extraAttributes([
                'class' => 'py-0',
            ])
            ->label('Add to Proposal');
    }

    public function removeFromCartAction(): Action
    {
        $self = $this;

        return Action::make('removeFromCart')
            ->action(function () use ($self) {
                $self->removeFromCart();
            })
            ->extraAttributes([
                'class' => 'py-0',
            ])

            ->requiresConfirmation()
            ->modalIcon('heroicon-o-trash')
            ->modalIconColor('warning') // TODO: make it work!
            ->modalHeading('Remove "'. $this->tool->name.'" from proposal')
            ->modalDescription('Are you sure you\'d like to remove this service?')
            ->modalSubmitActionLabel('Yes, remove it')

            ->label($this->removeButtonText);
    }

    public function addToFavourites()
    {
        if ($this->tool) {

            ERIHSFavouriteService::addItem($this->tool->id);

            $this->dispatch('refreshFavourites');

            Notification::make()
                ->title('Tool "'.$this->tool->name.' " added to favourites')
                ->success()
                ->send();
        }
    }

    public function removeFromFavourites(): void
    {
        if ($this->tool) {
            ERIHSFavouriteService::removeItem($this->tool->id);

            $this->dispatch('refreshFavourites');

            Notification::make()
                ->title('Tool "'.$this->tool->name.'" removed from favourites')
                ->success()
                ->send();
        }
    }

    public function addToFavouritesAction(): Action
    {
        $self = $this;

        return Action::make('addToFavourites')
            ->action(function () use ($self) {
                $self->addToFavourites();
            })
            ->link()
            ->extraAttributes([
                'class' => 'py-0',
            ])
            ->label('Save for later');
    }

    public function viewDetailsAction(): Action
    {

        return Action::make('viewDetails')
            ->url(fn (): string => route('tool', ['tool_id' => $this->tool->id]))
            // ->openUrlInNewTab()
            ->link()
            ->extraAttributes([
                'class' => 'py-0',
            ])
            ->label(new HtmlString(
                '<span style="display:flex">View details <svg class="shrink-0 w-[18px] h-[18px] relative overflow-visible" style="" width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5 8.9999C5 8.62711 5.30221 8.3249 5.675 8.3249H11.6491L9.70715 6.56147C9.43843 6.30308 9.43005 5.87578 9.68844 5.60705C9.94682 5.33833 10.3741 5.32996 10.6428 5.58834L13.7928 8.51334C13.9252 8.6406 14 8.81629 14 8.9999C14 9.18351 13.9252 9.3592 13.7928 9.48647L10.6428 12.4115C10.3741 12.6699 9.94682 12.6615 9.68844 12.3928C9.43005 12.124 9.43843 11.6967 9.70715 11.4383L11.6491 9.6749H5.675C5.30221 9.6749 5 9.37269 5 8.9999Z" fill="#0F172A"/>
                </svg></span>')
            );
    }

    public function removeFromFavouritesAction(): Action
    {
        $self = $this;

        return Action::make('removeFromFavourites')
            ->action(function () use ($self) {
                $self->removeFromFavourites();
            })
            ->link()

            // ->size(ActionSize::ExtraSmall)

            ->extraAttributes([
                'class' => 'py-0',
            ])
            ->modalIconColor('warning')
            ->modalIcon('heroicon-o-trash')
            // ->requiresConfirmation()
            ->label('Remove from Favourites');
    }

    public function render()
    {
        if ($this->showFavouritesInteractionButtons) {
            // can be overridden to false in including component, but if it's true, we
            // must check if the user is authenticated and only if they are we show the actions
            $this->showFavouritesInteractionButtons = ERIHSAuthService::checkLogged();
        }
        return view('livewire.tool-box');
    }
}
