<div>
    <div class="bg-gray-50">
        @livewire('public-page-header')
        <div wire:loading id="loading-overlay-proposal" class="loading-overlay">
            <div class="loading-overlay-image-container">
                <img src="/images/loading.gif" class="loading-overlay-img" />
            </div>
        </div>
        <div class="bg-catalogue-bg h-44 bg-primary-600">
            <div class="h-32 px-6 py-10 mt-10 ml-6 rounded-xl justify-start items-start gap-96 inline-flex">
                <div class="text-gray-50 text-3xl font-bold font-['Montserrat'] leading-10">
                    Catalogue of Services
                <!-- <div class="text-gray-50 text-sm font-bold font-['Montserrat'] leading-10">
                    The Catalogue of Services is a work in progress and may not yet include all services. We are working to expand and refine this resource. Thank you for your understanding
                </div> -->
            </div>

            </div>
        </div>

        @if ($elasticDown)
            <div class=" p-4 flex flex-row h-screen">
                <div class="p-4 overflow-auto max-h-screen">
                        <div class="text-gray-900 text-3xl font-bold font-['Montserrat'] leading-10">
                    The service is momentary down due to server-side maintenance.
                    <br/>
                    Please come back later.
                </div>

                </div>
            </div>

        @else

            <div class="grid lg:grid-cols-3 xl:grid-cols-4 p-4 flex flex-row h-screen">
                <div class="lg:col-span-1 p-4 overflow-auto max-h-screen">
                    <form wire:submit="submit">
                        <div class="mb-4">
                            {{ $this->searchForm }}
                        </div>
                        <div class="mb-4 catalogue-filters">
                            {{ $this->filterForm }}
                        </div>
                    </form>
                </div>
                <div id="results-pane" class="w-full lg:col-span-2 xl:col-span-3 xl:center p-4 overflow-auto max-h-screen">
                    <div class="w-full h-10 mb-4 justify-start items-center gap-4 inline-flex">
                        <div
                            class="grow shrink basis-0 text-neutral-900 text-xl font-semibold font-['Montserrat'] leading-7">
                            {{ $resultCount }} results
                        </div>
                        {{-- <div class="justify-start items-center gap-4 flex">
                            <div class="w-14 text-gray-700 text-sm font-semibold font-['Montserrat'] leading-tight">Sort
                                by
                            </div>
                            <div class="w-4data: ['scrollTo' => '#paginated-posts']) 4 flex-col justify-start items-start gap-1.5 inline-flex">
                                <div class="self-stretch justify-start items-center gap-2 inline-flex">
                                    <div
                                        class="grow shrink basis-0 h-10 px-3.5 py-2 rounded-lg shadow border border-gray-300 justify-start items-center gap-2.5 flex">
                                        <div
                                            class="grow shrink basis-0 text-gray-500 text-base font-normal font-['Montserrat'] leading-normal">
                                            Select option
                                        </div>
                                        <div class="w-5 h-5 relative"></div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    @if ($services)
                        @foreach ($services as $service)
                            @if ($service->model())

                                @php
                                    $random = microtime(true) * 1000;
                                @endphp

                                @livewire('service-box', ['service' => $service->model()], key('serviceID-' . $service->model()->id . '-' . $random))

                            @endif
                        @endforeach
                    @endif

                    <x-filament::pagination :paginator="$searchResult" :page-options="[10, 20, 50]" :current-page-option-property="'itemsPerPage'" />

                </div>
                <script>
                    document.addEventListener("catalogue-scroll-to-top", () => {
                        document.getElementById('results-pane').scrollTo({
                            top: 0,
                            behavior: "smooth"
                        })
                    })
                </script>

                <x-filament-actions::modals />
            </div>
        @endif
    </div>
</div>
