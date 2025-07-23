<div>
    @livewire('public-page-header')


    <br/>
    <br/>


    <x-filament-panels::page.simple>

        {{-- <div class="grid flex-1 auto-cols-fr gap-y-8" style="margin: 2em;">
            <div class="grid items-center justify-center"> --}}
            <div class=" gap-y-8" style="margin: 2em;">
                <x-filament-panels::form wire:submit="save">

                    {{ $this->form }}

                    <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
                </x-filament-panels::form>
            </div>
            {{-- </div>
        </div> --}}
    </x-filament-panels::page.simple>
</div>
