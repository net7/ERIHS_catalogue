<div class="
@if (!$hideOuterBorder) border border-gray-300 mb-4 @endif
w-full pt-6 px-6 bg-white rounded-xl  flex-col justify-start items-start gap-4 inline-flex">
    @php
    use App\Services\ERIHSFavouriteService;
    use App\Services\ERIHSCartService;
    use App\Models\Tool;
    use App\Models\Method;
    @endphp
    <div class="self-stretch justify-start items-start gap-16 inline-flex">
        <div class="grow shrink basis-0 min-h-6 justify-start items-start gap-2 flex">
            <div class="self-stretch grow shrink basis-0 text-gray-800 text-lg font-bold font-['Montserrat'] leading-relaxed">
                <a href="{{  route('service', ['id' => $this->service->id]) }}">
                {{ $service->title }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pb-1.5 size-6 w-5 h-6 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                      </svg>

                </a>
                <div class="pt-4 self-stretch text-neutral-900 text-sm font-normal font-['Montserrat'] leading-tight text-justify">
                    {{ $service->summary }}
                </div>
                <!-- <div class="pt-4 self-stretch text-neutral-900 text-sm font-normal font-['Montserrat'] leading-tight">
                    {{ $service->description }}
                </div> -->
            </div>
        </div>
        <div class="flex-col justify-start items-start gap-2 inline-flex">
            @if ($showCartInteractionButtons)
            <div class="self-stretch px-3 py-1.5 rounded-lg justify-center items-center gap-1.5 inline-flex">
                <div class="text-center text-gray-700 text-sm font-semibold font-['Montserrat'] leading-tight">
                    @if (ERIHSCartService::hasItem($service->id))
                    {{ $this->removeFromCartAction() }}
                    @else
                    {{ $this->addToCartAction() }}
                    @endif
                </div>
            </div>
            @else
            <div class="self-stretch px-3 py-1.5 rounded-lg justify-center items-center gap-1.5 inline-flex">
                <div class="text-center text-gray-700 text-sm font-semibold font-['Montserrat'] leading-tight">
                    <a href="{{ $service->url }}" target="_blank">
                        Go to the service page
                    </a>
                </div>
            </div>
            @endif

            @if ($showFavouritesInteractionButtons)
            <div class="self-stretch px-3 py-1.5 rounded-lg justify-center items-center gap-1.5 inline-flex">
                <div class="text-center text-gray-700 text-sm font-semibold font-['Montserrat'] leading-tight">
                    @if (ERIHSFavouriteService::hasItem($service->id))
                    {{ $this->removeFromFavouritesAction() }}
                    @else
                    {{ $this->addToFavouritesAction() }}
                    @endif
                </div>
            </div>
            @endif
{{--
            @if($showViewDetailsButton)
            <div class="self-stretch px-3 py-1.5 rounded-lg justify-center items-center gap-1.5 inline-flex">
                <div class="text-center text-gray-700 text-sm font-semibold font-['Montserrat'] leading-tight">
                    {{ $this->viewDetailsAction() }}

                </div>
            </div>
            @endif --}}
        </div>

    </div>

    {{-- <div class="self-stretch text-neutral-900 text-sm font-normal font-['Montserrat'] leading-tight">
        {{ $service->description }}
    </div> --}}
    <div class="self-stretch h-px flex-col justify-center items-start flex">
        <div class="w-full h-px relative bg-gray-200"></div>
    </div>

    <div class="self-stretch flex-col justify-start items-start gap-1 flex">

        @php 
            $badgeSpanOpen = "<span class='rounded-md bg-gray-50 text-sm border-gray-300  px-2 py-1  ring-1 ring-inset ring-gray-300 text-nowrap mr-1 mb-1 '>";
            $badgeSpanClose = "</span>";
        @endphp

        @php
            $title = 'Platforms';
            $data = $service->tagsWithType('e-rihs_platform')->pluck('name')->toArray();
            $icon = 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z';
            //$text = empty($data) ? 'No platforms found' : implode(', ', $data) ;
            $text = empty($data) ? 'No platforms found' : $badgeSpanOpen . implode($badgeSpanClose . ' ' . $badgeSpanOpen, $data) . $badgeSpanClose ;
        @endphp
        {{-- @include('partials.service-box-characteristic', ['title' => $title, 'text' => $text, 'icon' => $icon, 'bold' => false, 'titlePadding' => false] ) --}}
        @include('partials.service-box-characteristic', ['title' => $title, 'text' => $text, 'icon' => $icon, 'bold' => false, 'titlePadding' => true] )

        {{-- @php
            $title = 'Fields of application';
            $data = $service->researchDisciplines();
            $icon = 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z';
            $text = empty($data) ? 'No fields of applitcaion found' : implode(', ', $data) ;
        @endphp
        @include('partials.service-box-characteristic', ['title' => $title, 'text' => $text, 'icon' => $icon, 'bold' => false] )

        @php
            $title = 'Materials';
            $data = $service->materials();
            $icon = 'm21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9';
            $text = empty($data) ? 'No materials found' : implode(', ', $data) ;
        @endphp
        @include('partials.service-box-characteristic', ['title' => $title, 'text' => $text, 'icon' => $icon, 'bold' => false] ) --}}

        @php
            $title = 'Tools';
            $data = $service->getTools();
            $icon = 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z';
            $text = empty($data) ? 'No tools found' : $badgeSpanOpen . implode($badgeSpanClose . ' ' . $badgeSpanOpen, $data) . $badgeSpanClose;
        @endphp
        @if (!empty($data))
            @include('partials.service-box-characteristic', ['title' => $title, 'text' => $text, 'icon' => $icon, 'bold' => false, 'titlePadding' => true] )
        @endif

        @php
            $title = 'Techniques';
            if ($service->other_techniques) {
                $data = [...$service->tagsWithType('technique')->pluck('name')->toArray(), $service->other_techniques];
            } else {
                $data = $service->tagsWithType('technique')->pluck('name')->toArray();
            }
            $icon = 'M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008z';
            $text = empty($data) ? 'No techniques found' : $badgeSpanOpen . implode($badgeSpanClose . ' ' . $badgeSpanOpen, $data) . $badgeSpanClose;
        @endphp
        @include('partials.service-box-characteristic', ['title' => $title, 'text' => $text, 'icon' => $icon, 'bold' => false, 'titlePadding' => true] )

    </div>

    <div class="self-stretch h-px flex-col justify-center items-start flex">
        <div class="w-full h-px relative bg-gray-200"></div>
    </div>

    <div class="self-stretch justify-between items-end inline-flex">
        <div class="w-full flex-col justify-start items-start gap-2 inline-flex">
            <div class="text-gray-600 text-sm font-normal font-['Montserrat'] leading-tight">Organization</div>
            <div class="justify-start items-center gap-2 inline-flex">
                <div class="text-slate-600 text-sm font-semibold font-['Montserrat'] leading-tight">
                    {{ $service->organization->name }}
                </div>
                <div class="w-5 h-px gap-1 rotate-90 border border-gray-300"></div>
                <div class="justify-start items-center gap-2 flex">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>

                    </div>
                    <div class="text-slate-600 text-sm font-semibold font-['Montserrat'] leading-tight">
                        {{ implode(', ', $service->organization->tagsWithType('country')->pluck('name')->toArray()) }}
                    </div>
                </div>
            </div>
        </div>

    </div>
    <x-filament-actions::modals/>

</div>
