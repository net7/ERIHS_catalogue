<div> 
    @livewire('public-page-header')
    
    <x-filament::page>
        <div class="items-center justify-center">
            <div class="max-w-8xl mx-auto border-solid p-5">
                {{-- <div class="mx-auto border-solid p-5"> --}}
                    <form wire:submit="submit">
                    {{ $this->form }}
                </form>

            </div>

            <div class="flex flex-col items-center justify-center">
                <button wire:click="goToDashboard" type="button" class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600">
                    Skip profile completion
                </button>
            </div>

        </div>

    </x-filament::page>
</div>