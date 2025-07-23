
<div class="
@if (!$hideOuterBorder) border border-gray-300 mb-4 @endif
w-full p-6 bg-white rounded-xl  flex-col justify-start items-start gap-4 inline-flex">


@php
    use App\Services\ERIHSFavouriteService;
    use App\Services\ERIHSCartService;
@endphp
    <div class="self-stretch justify-start items-start gap-16 inline-flex">
        <div class="grow shrink basis-0 h-6 justify-start items-start gap-2 flex">
            <div class="grow shrink basis-0 text-gray-800 text-lg font-bold font-['Montserrat'] leading-relaxed">
                {{ $tool->name }}
            </div>
        </div>
        <div class="flex-col justify-start items-start gap-2 inline-flex">
            @if ($showCartInteractionButtons)
                <div class="self-stretch px-3 py-1.5 rounded-lg justify-center items-center gap-1.5 inline-flex">
                    <div class="text-center text-gray-700 text-sm font-semibold font-['Montserrat'] leading-tight">
                        @if (ERIHSCartService::hasTool($tool->id))
                            {{ $this->removeFromCartAction() }}
                        @else
                            {{ $this->addToCartAction() }}
                        @endif
                    </div>
                </div>
            @endif

            @if ($showFavouritesInteractionButtons)
                <div class="self-stretch px-3 py-1.5 rounded-lg justify-center items-center gap-1.5 inline-flex">
                    <div class="text-center text-gray-700 text-sm font-semibold font-['Montserrat'] leading-tight">
                        @if (ERIHSFavouriteService::hasTool($tool->id))
                            {{ $this->removeFromFavouritesAction() }}
                        @else
                            {{ $this->addToFavouritesAction() }}
                        @endif
                    </div>
                </div>
            @endif

            @if($showViewDetailsButton)
                <div class="self-stretch px-3 py-1.5 rounded-lg justify-center items-center gap-1.5 inline-flex">
                    <div class="text-center text-gray-700 text-sm font-semibold font-['Montserrat'] leading-tight">
                        {{ $this->viewDetailsAction() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="self-stretch h-px flex-col justify-center items-start flex">
        <div class="w-full h-px relative bg-gray-200"></div>
    </div>
    <div class="self-stretch h-24 flex-col justify-start items-start gap-6 flex">
        <div class="self-stretch h-12 flex-col justify-start items-start gap-2 flex">
            <div class="self-stretch justify-start items-center inline-flex">
                <div class="justify-start items-end gap-2 flex">
                    <div class="text-gray-600 text-sm font-normal font-['Montserrat'] leading-tight">Category:</div>
                    <div class="  justify-start items-center gap-1.5 flex">
                        @foreach ($tool->categories as $category)
                            {{-- {{$category->id}} --}}
                            <div
                                class="text-center text-gray-800 pl-2 pr-2 py-0.5 bg-gray-200 rounded-3xl text-sm font-semibold font-['Montserrat'] leading-tight">
                                {{ $category->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="self-stretch justify-start items-center inline-flex">
                <div class="justify-start items-end gap-2 flex">
                    <div class="text-gray-600 text-sm font-normal font-['Montserrat'] leading-tight">Platform:</div>
                    <div class="justify-start items-start gap-2 flex">
                        <div class="justify-start items-center gap-2 flex">
                            <div class="text-slate-600 text-sm font-bold font-['Montserrat'] leading-tight">
                                {{ $tool->platform->name }}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="self-stretch h-5 flex-col justify-start items-start gap-4 flex">
            <div class="self-stretch justify-start items-start gap-2 inline-flex">
                <div class="justify-start items-start gap-2 flex">
                    <div class="w-4 h-4 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008z" />
                        </svg>

                    </div>
                    <div class="text-gray-600 text-sm font-normal font-['Montserrat'] leading-tight">Technique:</div>
                </div>
                <div class="grow shrink basis-0 text-gray-800 text-sm font-semibold font-['Montserrat'] leading-tight">
                    {{-- {{$tool->technique->name}} --}}
                </div>
            </div>
        </div>
    </div>
    <div class="self-stretch text-neutral-900 text-sm font-normal font-['Montserrat'] leading-tight">
        {{ $tool->description }}
        {{-- 3D structure-light
        scanner: works with white light and does not acquire colour information. It is equipped with a CMOS 5 Megapixel
        camera and three different sets of lenses. The S60, with a field of view of 49x40mm and a maximum resolution of
        0.02mm. The S125, with a field of view of 116x98mm and a maximum… --}}
    </div>
    <div class="self-stretch justify-between items-end inline-flex">
        <div class="w-full flex-col justify-start items-start gap-2 inline-flex">
            <div class="text-gray-600 text-sm font-normal font-['Montserrat'] leading-tight">Organization</div>
            <div class="justify-start items-center gap-2 inline-flex">
                <div class="text-slate-600 text-sm font-semibold font-['Montserrat'] leading-tight">
                    {{ $tool->organization->name }}
                </div>
                <div class="w-5 h-px  gap-1 rotate-90 border border-gray-300"></div>
                <div class="justify-start items-center gap-2 flex">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>

                    </div>
                    <div class="text-slate-600 text-sm font-semibold font-['Montserrat'] leading-tight">
                        {{ $tool->organization->state }}
                    </div>
                </div>
            </div>
        </div>
        <div class="h-8 opacity-0 justify-start items-start gap-2.5 flex">
            <div class="w-8 h-8 p-1 justify-center items-center flex"></div>
        </div>
    </div>
    <x-filament-actions::modals />

</div>
