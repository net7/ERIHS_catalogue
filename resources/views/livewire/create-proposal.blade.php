<div>
    @livewire('public-page-header')
    <div wire:loading id="loading-overlay-proposal" class="loading-overlay">
        <div class="loading-overlay-image-container">
            <img src="/images/loading.gif" class="loading-overlay-img"/>
        </div>
    </div>
    @livewire('loading-overlay')
    {{-- <div class="max-w-6xl mb-10 mx-auto border-solid p-5"> --}}
    <div class="mb-10 mx-auto border-solid py-5 px-14">
            <form wire:submit="submit">
            <div>
                <p style="font-weight:200;">PROPOSAL<br>If you need assistance with completing the application form, you
                    can:</p>
                    <ol style="list-style-type: decimal; margin-left: 30px;">
                        <li> Hover over the question mark icon to view additional information.</li>
                        {{-- <li> Watch the instructional video at the following (link)</li> --}}
                        <li> Contact the User Helpdesk via email at: <i>helpdesk@e-rihs.eu</i>.</li>
                    </ol>
                    <p>&nbsp;</p>
                    <hr>
                    <p>&nbsp;</p>

            </div>
            {{ $this->proposalForm }}

            <p>&nbsp;</p>
            <x-filament::button wire:click="saveAsDraft">Save as draft</x-filament::button>
        </form>
    </div>
    @include('livewire/validation-scroll-to-error')
    <x-filament-actions::modals/>
</div>
