<div class="bg-gray-50">
    @livewire('public-page-header')

    <div class="grid sm:col-span-1 md:grid-cols-4 p-4  ml-5 mb-10 ">
        <div class="sm:col-span-1 md:col-span-4 justify-between items-end inline-flex p-2 mb-2">
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
        <div class="sm:col-span-1 md:col-span-4 justify-between items-end inline-flex p-2 mb-2">
            <div class="text-gray-900 text-[32px] font-bold font-['Montserrat'] leading-[48px]">
                Tool: {{ $tool->name }}
            </div>
        </div>

        <div class="sm:col-span-1 md:col-span-2  px-5 py-4 bg-white rounded-xl border border-gray-300 flex-col justify-end items-end gap-4 inline-flex mr-10">
            <div class="self-stretch  flex-col justify-start items-start gap-6 flex">
                <div class="self-stretch  flex-col justify-start items-start gap-4 flex">
                    <div class="w-10 h-10 relative text-[#e30613]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path
                                d="M5.507 4.048A3 3 0 0 1 7.785 3h8.43a3 3 0 0 1 2.278 1.048l1.722 2.008A4.533 4.533 0 0 0 19.5 6h-15c-.243 0-.482.02-.715.056l1.722-2.008Z">
                            </path>
                            <path fill-rule="evenodd"
                                d="M1.5 10.5a3 3 0 0 1 3-3h15a3 3 0 1 1 0 6h-15a3 3 0 0 1-3-3Zm15 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm2.25.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM4.5 15a3 3 0 1 0 0 6h15a3 3 0 1 0 0-6h-15Zm11.25 3.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM19.5 18a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                                clip-rule="evenodd">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-col justify-start items-start gap-1 flex">
                        <div class="text-gray-700 text-base font-normal font-['Montserrat'] leading-normal">
                            Organization
                        </div>
                        <div class=" text-gray-800 text-lg font-semibold font-['Montserrat'] leading-relaxed">
                            {{ $tool->organization->name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1  md:col-start-4 px-5 py-4 bg-white rounded-xl border border-gray-300 flex-col justify-end items-end gap-4 inline-flex mr-10">
            <div class="self-stretch h-[110px] flex-col justify-start items-start gap-6 flex">
                <div class="self-stretch h-[110px] flex-col justify-start items-start gap-4 flex">
                    <div class="w-10 h-10 relative text-[#e30613]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                    </div>
                    <div class="flex-col justify-start items-start gap-1 flex">
                        <div class="text-gray-700 text-base font-normal font-['Montserrat'] leading-normal">
                            Last checked date
                        </div>
                        <div class="w-[382px] text-gray-800 text-lg font-semibold font-['Montserrat'] leading-relaxed">
                            {{ $tool->last_checked_date ? date('d-m-Y', strtotime($tool->last_checked_date)) : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sm:col-span-1 md:col-span-4 flex-col justify-start items-start gap-1 inline-flex mt-10 mr-10 border-b-2  pb-10">
            <div class="text-gray-700 text-xl font-bold font-['Montserrat'] leading-normal">
                Description
            </div>
            <div class="self-stretch text-gray-800 text-base font-normal font-['Montserrat'] leading-normal">
                {{ $tool->description }}
            </div>
        </div>

        @if (!$output_data_types->isEmpty())
            @include('partials.tool-item-characteristic', [
                'title' => 'Output and data types',
                'data' => $output_data_types,
            ])
        @endif

        @if (!$impact_on_object->isEmpty())
            @include('partials.tool-item-characteristic', [
                'title' => 'Impact on object or sample',
                'data' => $impact_on_object,
            ])
        @endif

        @if ($tool->manufacturer)
            @include('partials.tool-item-characteristic', [
                'title' => 'Manufacturer',
                'data' => $tool->manufacturer,
            ])
        @endif

        @if ($tool->model)
            @include('partials.tool-item-characteristic', ['title' => 'Model', 'data' => $tool->model])
        @endif

        @if (!$acquisition_areas->isEmpty())
            @include('partials.tool-item-characteristic', [
                'title' => 'Acquisition areas',
                'data' => $acquisition_areas,
            ])
        @endif

        @if (!$working_distances->isEmpty())
            @include('partials.tool-item-characteristic', [
                'title' => 'Working distances',
                'data' => $working_distances,
                'last' => true,
            ])
        @endif
    </div>
</div>
