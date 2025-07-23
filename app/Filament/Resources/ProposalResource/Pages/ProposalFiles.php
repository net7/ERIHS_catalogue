<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Enums\MolabOwnershipConsent;
use App\Filament\Resources\ProposalResource;
use App\Livewire\CreateProposal;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Form;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\EditRecord;

class ProposalFiles extends EditRecord
{
    use HasPageSidebar;
    use InteractsWithRecord;

    protected static string $resource = ProposalResource::class;

    protected static ?string $breadcrumb = "Proposal files";
    protected static ?string $title = "Proposal files";


    protected static string $view = 'filament.resources.proposal-resource.pages.proposal-files';

    public function getModel(): string
    {
        return static::getResource()::getModel();
    }

    public function form(Form $form): Form
    {
        $services = CreateProposal::getServices(proposalId: $this->record->id);
        $platforms = CreateProposal::getPlatforms(services: $services);
        $schema = CreateProposal::getMolabSection(hasMolab: $platforms, isDraft: false, updateFiles: true);

        return $form
            ->schema([
                $schema
            ])
            ->columns(1);
    }


    public function submit(): void
    {
        // Get the current form state
        $formState = $this->form->getState();

        // Manually handle any dynamic logic
        $this->handleDynamicFields($formState);

        // Save the updated state
        $this->form->fill($formState);
        $this->save();
    }


    protected function handleDynamicFields(array &$formState): void
    {
        // Check if 'molab_objects_data' exists in the form state
        if (isset($formState['molab_objects_data'])) {
            foreach ($formState['molab_objects_data'] as $index => $data) {
                // If "Other" is selected, clear the associated file field
                if (isset($data['molab_object_ownership_consent']) && $data['molab_object_ownership_consent'] === MolabOwnershipConsent::OTHER->name) {
                    $formState['molab_objects_data'][$index]['molab_object_ownership_consent_file'] = null;
                }
            }
        }

        if (isset($formState['molab_drone_flight'])) {
            if ($formState['molab_drone_flight'] === 'other' || $formState['molab_drone_flight'] === 'non_applicable') {
                $this->record->setAttribute('molab_drone_flight_file', null);
            }
        }

        if (isset($formState['molab_x_ray']) && !$formState['molab_x_ray']) {
            $this->record->setAttribute('molab_x_ray_file', null);
        }
    }

}
