<?php

namespace App\Livewire;

use Livewire\Attributes\Reactive;

use App\Services\ERIHSCartService;
use Livewire\Component;

class PublicPageHeader extends Component
{
    public $cartCount;

    protected $listeners = ['refreshCart' => 'updateCount'];

    public $hideCartLink = false;

    public function mount(){
       $this->updateCount();
    }

    public function updateCount(){
        $this->cartCount = ERIHSCartService::getItemsCount();
    }

    public function render()
    {
        return view('livewire.public-page-header');
    }
}
