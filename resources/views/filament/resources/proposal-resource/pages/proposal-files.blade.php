<x-filament-panels::page>
    {{ $this->form }}
</x-filament-panels::page>
<div style="display: flex; justify-content: center;" class="pb-6">
    <x-filament::button
        type="submit"
        wire:click="submit"
        wire:target="submit">
        Save
    </x-filament::button>
</div>
