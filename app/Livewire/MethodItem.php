<?php

namespace App\Livewire;

use App\Models\Service;
use App\Models\Method;
use App\Services\ERIHSAuthService;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Tags\Tag;

class MethodItem extends Component
{
    use WithPagination;

    public $method;
    public $output_data_types;
    public $impact_on_object;
    public $acquisition_areas;
    public $working_distances;
    public $service_id;

    public function mount($service_id, $id): void
    {
        $this->service_id = $service_id;
        $this->method = Method::findOrFail($id);
        $this->output_data_types = $this->method->tagsWithType('method_output_data_types');
        $this->impact_on_object = $this->method->tagsWithType('object_impact');
        $this->acquisition_areas = $this->method->tagsWithType('method_equipment_acquisition_areas');
        $this->working_distances = $this->method->tagsWithType('working_distance');
    }

    public function render()
    {
        $service = Service::find($this->service_id);
        if (!$service || !$service->isActive()) {

            Notification::make()
            ->title(title: 'Method unavailable!')
            ->body('The requested method doesn\'t exist')
            ->warning()
            ->send();

            $this->redirect('catalogue');
        }

        return view('livewire.method-item');
    }
}
