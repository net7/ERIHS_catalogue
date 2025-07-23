<div>

    @livewire('public-page-header')

    <x-filament::page>
        <div class="items-center justify-center">
            <div class="max-w-4xl mx-auto border-solid p-5">
                {{-- <div class="mx-auto border-solid p-5"> --}}
                    <form wire:submit="submit">
                        {{ $this->form }}
                    </form>
                </div>
    </x-filament::page>

</div>