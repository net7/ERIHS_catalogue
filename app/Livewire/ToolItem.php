<?php

namespace App\Livewire;

use App\Models\Service;
use App\Models\Tool;
use App\Services\ERIHSAuthService;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Tags\Tag;

class ToolItem extends Component
{
    use WithPagination;

    public $tool;
    public $output_data_types;
    public $impact_on_object;
    public $acquisition_areas;
    public $working_distances;
    public $service_id;

    public function mount($service_id, $id): void
    {
        $this->service_id = $service_id;
        $this->tool = Tool::findorFail($id);

        $this->output_data_types = $this->tool->tagsWithType('tool_output_data_types');
        $this->impact_on_object = $this->tool->tagsWithType('object_impact');
        $this->acquisition_areas = $this->tool->tagsWithType('tool_equipment_acquisition_areas');
        $this->working_distances = $this->tool->tagsWithType('working_distance');
    }

    public function render()
    {
        $service = Service::find($this->service_id);

        if (!$service || !$service->isActive()) {

            Notification::make()
            ->title(title: 'Tool unavailable!')
            ->body('The requested tool doesn\'t exist')
            ->warning()
            ->send();

            $this->redirect('catalogue');
        }

        return view('livewire.tool-item');
    }
}
