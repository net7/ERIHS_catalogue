@php
    use App\Services\ERIHSFavouriteService;
    use App\Services\ERIHSCartService;
    use App\Models\Tool;
    use App\Models\Method;
@endphp

<div class="bg-gray-50">

    @livewire('public-page-header')

    <div class="grid sm:grid-cols-1  lg:grid-cols-3 xl:grid-cols-4 p-4 flex flex-row mb-10">
        {{-- top zone --}}
        <div
            class="sm:col-span-1  lg:col-span-3 xl:col-span-4 justify-between items-end inline-flex border-b-2 p-2 mb-10">
            <div class="flex-col justify-start items-start gap-4 inline-flex">
                <div class="justify-start items-center gap-0.5 inline-flex">
                    <div class="w-4 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>

                    </div>
                    <div class="text-slate-600 text-base font-normal  leading-normal">
                        <a href="/catalogue">Go back to catalogue</a>
                    </div>
                </div>
                <div class="text-neutral-900 text-[32px] font-bold  leading-[48px]">
                    Organization: {{ $organization->name }}
                </div>
            </div>
        </div>

        {{-- bottom zone --}}
        <div class="w-full sm:col-span-1 lg:col-span-2 xl:col-span-3 xl:center col-start-1 p-4 ">
            <div class="grid sm:grid-cols-1 md:grid-cols-2 self-stretch flex-col justify-start items-start gap-16 ">
                <div
                    class="sm:col-span-1 md:col-span-1 justify-start gap-1 mt-10 mr-10 border-b-2  pb-10">
                    <div class="text-gray-700 text-xl font-bold font-['Montserrat'] leading-normal">
                        Acronym
                    </div>
                    <div
                        class="self-stretch text-gray-800 text-base font-normal font-['Montserrat'] leading-normal">
                        {{ $organization->acronym }}
                    </div>
                </div>

                <div
                    class="sm:col-span-1 md:col-span-1 justify-start gap-1 mt-10 mr-10 border-b-2  pb-10">
                    <div class="text-gray-700 text-xl font-bold font-['Montserrat'] leading-normal">
                        Countries
                    </div>
                    <div
                        class="self-stretch text-gray-800 text-base font-normal font-['Montserrat'] leading-normal">
                        {{ implode(', ', $organization->getCountries()) }}
                    </div>
                </div>

                <div
                    class="sm:col-span-1 md:col-span-1 justify-start gap-1 mt-10 mr-10 border-b-2  pb-10">
                    <div class="text-gray-700 text-xl font-bold font-['Montserrat'] leading-normal">
                        Fields of application
                    </div>
                    <div
                        class="self-stretch text-gray-800 text-base font-normal font-['Montserrat'] leading-normal">
                        @forelse ($organization->getResearchDisciplines() as $field)
                                <span class="inline-flex items-center rounded-md bg-white  border-gray-300  px-2 py-1 font-medium  ring-1 ring-inset ring-gray-300 mb-1 ">
                                    {{ $field }}
                                </span>
                        @empty
                            {{-- <span class="inline-flex items-center rounded-md bg-white  border-gray-300  px-2 py-1 font-medium  ring-1 ring-inset ring-gray-300 mb-1 ">
                                No fields of application
                            </span> --}}
                        @endforelse
                    </div>
                </div>
                <div
                    class="sm:col-span-1 md:col-span-1 justify-start gap-1 mt-10 mr-10 border-b-2  pb-10">
                    <div class="text-gray-700 text-xl font-bold font-['Montserrat'] leading-normal">
                        Website
                    </div>
                    <div
                        class="self-stretch text-gray-800 text-base font-normal font-['Montserrat'] leading-normal">
                        @if ($organization->webpages)
                            @foreach ($organization->webpages as $webpage)
                                @if ($webpage['url'])
                                    <a href="{{ $webpage['url'] }}" target="_blank">{{ $webpage['url'] }}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="pb-1.5 size-6 w-5 h-6 inline-block">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </a>
                                <br/>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full sm:col-span-1 lg:col-span-1 xl:col-span-1 sm:col-start-1 lg:col-start-3 xl:col-start-4 p-4 ">
            <div class="sm:col-span-1 md:col-span-2 self-stretch flex-col justify-start items-start gap-4 flex">
                <div class=" ml-4">
                    @if ($organization->img_url)
                        <img class="rounded-xl mb-3 max-w-80"
                            src="{{ $organization->img_url ? $organization->img_url : 'http://via.placeholder.com/440x286' }}" />
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>