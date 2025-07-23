<div class="bg-gray-50">
    @livewire('public-page-header')

    <div class="grid sm:grid-cols-1 md:grid-cols-4 p-4  ml-5 mb-10">
        <div class="sm:col-span-1  md:col-span-4 justify-between items-end inline-flex  p-2 mb-2">
            <div class="justify-start items-center gap-0.5 inline-flex">
                <div class="w-4 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </div>
                <div class="text-slate-600 text-base font-normal font-['Montserrat'] leading-normal">
                    <a href="/service/{{ $service_id }}">
                        Go back to service
                    </a>
                </div>
            </div>
        </div>

        <div class="sm:col-span-1 md:col-span-4 justify-between items-end border-b-2 inline-flex p-2 mb-2">
            <div class="text-gray-900 text-[32px] font-bold font-['Montserrat'] leading-[48px]">
                Method: {{ $method->preferred_label }}
            </div>
        </div>

        <div class="sm:col-span-1 md:col-span-1 px-5 py-4 bg-white rounded-xl border border-gray-300 flex-col justify-end items-end gap-4 inline-flex mr-10">
            <div class="self-stretch  flex-col justify-start items-start gap-6 flex">
                <div class="self-stretch  flex-col justify-start items-start gap-4 flex">
                    <div class="w-10 h-10 relative text-[#e30613]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path
                                d="M5.507 4.048A3 3 0 0 1 7.785 3h8.43a3 3 0 0 1 2.278 1.048l1.722 2.008A4.533 4.533 0 0 0 19.5 6h-15c-.243 0-.482.02-.715.056l1.722-2.008Z">
                            </path>
                            <path fill-rule="evenodd"
                                d="M1.5 10.5a3 3 0 0 1 3-3h15a3 3 0 1 1 0 6h-15a3 3 0 0 1-3-3Zm15 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm2.25.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM4.5 15a3 3 0 1 0 0 6h15a3 3 0 1 0 0-6h-15Zm11.25 3.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM19.5 18a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-col justify-start items-start gap-1 flex">
                        <div class="text-gray-700 text-base font-normal font-['Montserrat'] leading-normal">
                            Organization
                        </div>
                        <div class=" text-gray-800 text-lg font-semibold font-['Montserrat'] leading-relaxed">
                            <a href="{{ route('organization', ['id' => $method->organization->id]) }}">
                                {{ $method->organization->name }}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="pb-1.5 size-6 w-5 h-6 inline-block">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </a>  
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($method->alternative_labels)
            @include('partials.tool-item-characteristic', [
                'title' => 'Alternative labels',
                'data' => collect($method->alternative_labels)->pluck('alternative_codes')->toArray(),
                'plain' => true,
            ])
        @endif
        <div
            class="sm:col-span-1 md:col-span-4 flex-col justify-start items-start gap-1 inline-flex mt-10 mr-10 border-b-2  pb-10">
            <div class="text-gray-700 text-xl font-bold font-['Montserrat'] leading-normal">
                Description
            </div>
            <div class="self-stretch text-gray-800 text-base font-normal font-['Montserrat'] leading-normal">
                {{ $method->method_documentation }}
            </div>
        </div>

        @if ($method->techniques())
            @include('partials.tool-item-characteristic', [
                'title' => 'Techniques',
                'data' => $method->techniques(),
            ])
        @endif

        @if ($method->technique_other)
            @include('partials.tool-item-characteristic', [
                'title' => 'Other techniques',
                'data' => $method->technique_other,
            ])
        @endif

        @if ($method->method_type)
            @include('partials.tool-item-characteristic', [
                'title' => 'Type',
                'data' => $method->method_type,
                'short' => true,
            ])
        @endif
        @if ($method->method_version)
            @include('partials.tool-item-characteristic', [
                'title' => 'Version',
                'data' => $method->method_version,
                'short' => true,
            ])
        @endif

        @if ($parameters = $method->parameters())
            <div
                class="grid sm:col-span-1 md:col-span-4  grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-6 flex-col justify-start items-start gap-1 inline-flex mt-10 mr-10 pb-10 @if (!isset($last) || !$last) border-b-2 @endif">
                <div class="sm:col-span-1 md:col-span-2 lg:col-span-4 2xl:col-span-6 flex-row">
                    <div class="text-gray-700 text-xl font-bold font-['Montserrat'] leading-normal mb-4">
                        Parameters
                    </div>
                    <div class="grid  grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-6">
                        @foreach ($parameters as $parameter)
                            {{-- <div class="inline-flex items-center rounded-md bg-white  border-gray-300  px-2 py-1 font-medium  ring-1 ring-inset ring-gray-300 mb-2 mr-1">

                    <div class="self-stretch text-gray-800 text-base font-normal font-['Montserrat'] leading-normal"> --}}
                            <div
                                class="self-stretch col-span-1 text-gray-800 text-base font-normal font-['Montserrat'] leading-normal bg-white  border-gray-300  px-2 py-1 font-medium  ring-1 ring-inset ring-gray-300 mb-2 mr-1">

                                {{-- <div class="inline-flex items-center rounded-md bg-white  border-gray-300  px-2 py-1 font-medium  ring-1 ring-inset ring-gray-300 mb-2 mr-1"> --}}
                                <div> <b>Type</b>: {{ $parameter['type'] }} </div>
                                <div> <b>Unit</b>: {{ $parameter['unit'] }} </div>
                                <div> <b>Value</b>: {{ $parameter['value'] }} </div>
                                @if ($parameter['tool'])
                                    <div> <b>Tool</b>: {{ $parameter['tool'] }} </div>
                                @endif
                                {{-- </div> --}}
                                {{-- </div> --}}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif


    </div>
</div>
