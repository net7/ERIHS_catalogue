<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Organization;

class OrganizationItem extends Component
{
    public $organization;

    public function mount($id): void
    {
        $this->organization = Organization::findOrFail($id);
    }
    public function render()
    {
        return view('livewire.organization-item');
    }
}
