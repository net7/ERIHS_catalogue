<?php

namespace App\Livewire;

use App\Models\Service;
use App\Services\ERIHSAuthService;
use App\Services\ERIHSFavouriteService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Tags\Tag;

class ServiceItem extends ServiceBox
{
    use WithPagination;

    public $service;
    public $countries;
    public $platform;

    public $contacts;
    public $techniques;
    public $materials;
    public $fields_of_application;
    public $service_managers;
    public $operating_languages;

    public $research_disciplines;
    // public $service_manager_mail;
    public $links;

    public function mount($id): void
    {
        $this->service = Service::findOrFail($id);
        $this->countries = $this->service->organization->tagsWithType('country');
        $this->platform = $this->service->tagsWithType('e-rihs_platform');
        $this->operating_languages = $this->service->tagsWithType(type: 'operating_language');
        $this->research_disciplines = $this->service->tagsWithType(type: 'research_disciplines');


        $this->techniques = $this->service->tagsWithType('technique');
        $this->materials = $this->service->materials();
        $this->service_managers = null;
        foreach ($this->service->serviceManagers as $serviceManager){
            $this->service_managers []=
            [
                'name' => $serviceManager->getFilamentName(),
                'email' =>  $serviceManager->email,
            ];
        }

        $this->contacts = $this->service->contacts;
        $this->links = [];
        if ($this->service->links) {
            $this->links = $this->service->links;
        }
        $this->fields_of_application = $this->service->tagsWithType('research_disciplines');
    }

    public function render()
    {

        if ( !$this->service->isActive()) {

            Notification::make()
            ->title(title: 'Service unavailable!')
            ->body('The requested service doesn\'t exist')
            ->warning()
            ->send();

            $this->redirect('catalogue');
        }

        if ($this->showFavouritesInteractionButtons) {
            // can be overridden to false in including component, but if it's true, we
            // must check if the user is authenticated and only if they are we show the actions
            $this->showFavouritesInteractionButtons = ERIHSAuthService::checkLogged();
        }
        if(!$this->service->application_required){
            $this->showCartInteractionButtons = false;
        }
        return view('livewire.service-item');
    }
}
