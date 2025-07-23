@vite('resources/css/app.css')
@livewire('public-page-header', ['hideCartLink' => true])
<div class="flex flex-col items-center justify-center h-screen bg-white">
    <div class="w-full max-w-[600px] px-4 flex flex-col items-center">
        <div id="this" class="flex flex-col justify-center items-center gap-4 my-16">
            <img src="https://file.rendit.io/n/9DkmOZOIFkpQYqft8mTD.svg" class="w-16"/>
            <div class="text-center text-3xl font-['Montserrat'] font-bold leading-[48px] text-[#111827]">
                Submitted!
                <br/>
            </div>
            <x-filament::button
                href="{{ App\Filament\Resources\MyProposalResource::getUrl('general-info', ['record' => $proposal_id]) }}"
                tag="a"
                color="gray">
                See your proposal: {{ \App\Models\Proposal::find($proposal_id)->name }}
            </x-filament::button>

            <x-filament::button href="{{ route('dashboard') }}" tag="a">Go back to the Dashboard</x-filament::button>

        </div>
    </div>
</div>
