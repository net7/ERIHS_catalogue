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
        <div class="sm:col-span-1  lg:col-span-3 xl:col-span-4 justify-between items-end inline-flex border-b-2 p-2 mb-10">
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
                    Service: {{ $service->title }}
                </div>
            </div>
            <div class="justify-start items-start gap-2 flex">
                @if ($showFavouritesInteractionButtons)
                    <div class="px-4 py-2.5 rounded-lg justify-center items-center gap-1.5 flex">
                        <div class="text-center text-gray-700 text-sm font-semibold  leading-tight">
                            @if (ERIHSFavouriteService::hasItem($service->id))
                                {{ $this->removeFromFavouritesAction() }}
                            @else
                                {{ $this->addToFavouritesAction() }}
                            @endif
                        </div>
                    </div>
                @endif
                @if ($showCartInteractionButtons)
                    @if (ERIHSCartService::hasItem($service->id))
                        {{ $this->removeFromCartAction() }}
                    @else
                        {{ $this->addToCartAction() }}
                    @endif
                @endif
            </div>
        </div>

        {{-- left pane --}}
        <div class="w-full col-span-1  xl:center  p-4">
            <div
                class="self-stretch px-5 pb-2 mb-3 bg-white rounded-xl border border-gray-300 flex-col justify-start items-start gap-2 flex">
                <div class="self-stretch h-4 flex-col justify-start items-start gap-2.5 flex">
                    <div class="w-[50px] h-4 bg-lime-300"></div>
                </div>
                <div class="self-stretch justify-start items-end inline-flex">
                    <div class="flex-col justify-start items-start gap-0.5 inline-flex">
                        <div class="text-gray-600 text-base font-normal  leading-normal">Platforms</div>
                        <div class="flex-col justify-start items-start gap-2 flex">
                            @forelse ($platform as $p)
                                <div class="text-gray-900 text-xl font-semibold leading-7">
                                    {{ $p->name }}
                                </div>
                            @empty
                                <div class="self-stretch text-gray-800 text-lg font-semibold leading-7">No PLatofrms
                                    found </div>
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>

            <div
                class="self-stretch px-5 py-6 mb-3 bg-white rounded-xl border border-gray-300 justify-between items-end">
                <div class="grow shrink basis-0 justify-start items-start gap-4 flex">
                    <div class="w-10 h-10 relative text-[#e30613]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path fill-rule="evenodd"
                                d="M2.25 4.125c0-1.036.84-1.875 1.875-1.875h5.25c1.036 0 1.875.84 1.875 1.875V17.25a4.5 4.5 0 1 1-9 0V4.125Zm4.5 14.25a1.125 1.125 0 1 0 0-2.25 1.125 1.125 0 0 0 0 2.25Z"
                                clip-rule="evenodd" />
                            <path
                                d="M10.719 21.75h9.156c1.036 0 1.875-.84 1.875-1.875v-5.25c0-1.036-.84-1.875-1.875-1.875h-.14l-8.742 8.743c-.09.089-.18.175-.274.257ZM12.738 17.625l6.474-6.474a1.875 1.875 0 0 0 0-2.651L15.5 4.787a1.875 1.875 0 0 0-2.651 0l-.1.099V17.25c0 .126-.003.251-.01.375Z" />
                        </svg>
                    </div>
                    <div class="self-stretch text-gray-700 text-base font-normal  leading-normal">
                        Techniques
                    </div>
                </div>
                <div class="flex-col justify-end items-end gap-0.5 inline-flex">
                    @forelse ($techniques as $technique)
                        <span
                            class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-s font-medium  ring-1 ring-inset ring-gray-500/10  self-stretch font-semibold leading-relaxed mb-1">
                            {{ $technique->name }}
                        </span>
                    @empty
                        <div class="self-stretch text-gray-800  font-semibold  leading-relaxed">No
                            techniques found</div>
                    @endforelse

                    @if ($service->other_techniques)
                        <span
                            class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-s font-medium  ring-1 ring-inset ring-gray-500/10  self-stretch font-semibold leading-relaxed mb-1">
                            {{ $service->other_techniques }}
                        </span>
                    @endif
                </div>
            </div>


            @if ($service->picture)
                <img class="self-stretch rounded-xl mb-3"
                    src="{{ $service->picture ? URL::asset('storage/' . $service->picture) : 'http://via.placeholder.com/440x286' }}" />
            @endif

            <div
                class="self-stretch px-5 py-6 mb-3 bg-white rounded-xl border border-gray-300 justify-between items-end">
                <div class="grow shrink basis-0 justify-start items-start gap-4 flex">
                    <div class="w-10 h-10 relative text-[#e30613]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path
                                d="M5.507 4.048A3 3 0 0 1 7.785 3h8.43a3 3 0 0 1 2.278 1.048l1.722 2.008A4.533 4.533 0 0 0 19.5 6h-15c-.243 0-.482.02-.715.056l1.722-2.008Z" />
                            <path fill-rule="evenodd"
                                d="M1.5 10.5a3 3 0 0 1 3-3h15a3 3 0 1 1 0 6h-15a3 3 0 0 1-3-3Zm15 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm2.25.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM4.5 15a3 3 0 1 0 0 6h15a3 3 0 1 0 0-6h-15Zm11.25 3.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM19.5 18a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="self-stretch text-gray-700 text-base font-normal  leading-normal">
                        Organization
                    </div>
                </div>


            @if ($service->organization->img_url)
                <img class="self-stretch rounded-xl mb-3"
                    src="{{ $service->organization->img_url ? $service->organization->img_url : 'http://via.placeholder.com/440x286' }}" />
            @endif
                <div class="flex flex-col justify-start mb-3 py-4 items-start gap-1">
                    <div class="text-gray-800 text-lg font-semibold leading-relaxed">
                        <a href="{{ route('organization', ['id' => $service->organization->id]) }}">
                            {{ $service->organization->name }}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="pb-1.5 size-6 w-5 h-6 inline-block">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>  
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Contenitore flex per allineamento orizzontale -->
                        <div class="flex-shrink-0"> <!-- Contenitore per l'SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="text-slate-600 text-sm font-semibold font-['Montserrat'] leading-tight">
                            @forelse ($countries as $index => $tag)
                                {{ $tag->name }}@if (!$loop->last)
                                    ,
                                @endif
                            @empty
                                No address found
                            @endforelse
                        </div>
                    </div>
                </div>
                @if ($contacts)
                <div class="text-gray-600 text-base font-normal  leading-normal">
                    Service contact persons
                </div>
                @foreach ($contacts as $contact)

                <div class="mt-5 w-full border-t border-gray-300"></div>
                <div class="self-stretch flex-col justify-start items-start gap-2 flex pt-2">
                    <div class="self-stretch flex-col justify-start items-start gap-0.5 flex">

                        {{-- <div class="self-stretch text-gray-800 text-lg font-semibold  leading-relaxed">
                            {{ $contact['name'] }}
                        </div> --}}
                    </div>
                    <div class="self-stretch justify-start items-start gap-2 inline-flex">
                        <div class="px-0.5 py-1 justify-start items-start gap-2.5 flex">
                            <div class="w-4 h-4 relative">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                </svg>

                            </div>
                        </div>
                        <div class="grow shrink basis-0  text-base font-normal  leading-normal pt-1 ">
                            <div class="line-clamp-1 text-blue-600" >
                                <a href="mailto:{{ $contact['email'] }}" title="{{ $contact['email'] }}">{{ Illuminate\Support\Str::of($contact['email'])->limit( 30) }}</a>
                            </div>

                            @if ($contact['phone'])
                                <div class="line-clamp-1">
                                    Phone:{{ Illuminate\Support\Str::of($contact['phone'])->limit( 30) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- </div> --}}
                </div>
                @endforeach
                @endif
            </div>
            @if ($links)
                <div
                    class="self-stretch px-5 py-6 mb-3 bg-white rounded-xl border border-gray-300 flex-col justify-start items-start gap-2 flex">
                    <div class="self-stretch">
                        <span class="text-gray-800 text-base font-semibold leading-normal">
                            Reference<br />
                        </span>
                        @forelse ($links as $link)
                            <div style="text-blue-600 text-base font-normal  leading-normal">
                                <a href="{{ $link['url'] }}" target="_blank">
                                {{ Spatie\Tags\Tag::find($link['type_tag_field'])->name }}
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="pb-1.5 size-6 w-5 h-6 inline-block">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                                </a>
                            </div>
                        @empty
                        @endforelse

                    </div>
                </div>
            @endif
        </div>

        {{-- right pane  --}}
        <div class="w-full sm:col-span-1  lg:col-span-2 xl:col-span-3 xl:center  p-4">
                <div class="grid sm:grid-cols-1 md:grid-cols-2 self-stretch flex-col justify-start items-start gap-16 flex">
                    <div class="sm:col-span-1 md:col-span-2 self-stretch flex-col justify-start items-start gap-4 flex">
                        <div class="text-black text-base font-normal  leading-normal text-justify">
                            {{ $service->summary }}
                        </div>
                        <div class="text-black text-base font-normal  leading-normal text-justify">
                            {{ $service->description }}
                        </div>

                        @if (isset($service->limitations))
                            <div class="text-black text-base font-normal  leading-normal text-justify">
                                {{ $service->limitations }}
                            </div>
                        @endif

                    </div>
                    <div class="grid sm:grid-cols-1 md:grid-cols-2 sm:col-span-1 md:col-span-2 self-stretch justify-start items-start gap-14 inline-flex">
                        @if (!$fields_of_application->isEmpty())
                            <div class="col-span-1 grow shrink basis-0 flex-col justify-start items-start gap-6 inline-flex">
                                <div class="text-gray-800 text-2xl font-semibold  leading-[30px]">
                                    Fields of application
                                </div>
                                <div class="self-stretch">
                                    {{-- <ul> --}}
                                        @forelse ($fields_of_application as $field)
                                            {{-- <li> --}}
                                                {{-- <span style="text-neutral-900 text-base font-semibold  leading-normal"> --}}
                                                <span class="inline-flex items-center rounded-md bg-white  border-gray-300  px-2 py-1 font-medium  ring-1 ring-inset ring-gray-300 mb-1 ">
                                                    {{ $field->name }}
                                                </span>
                                            {{-- </li> --}}

                                        @empty
                                        @endforelse
                                    {{-- </ul> --}}
                                </div>
                            </div>
                        @endif
                        @if (isset($materials) && !empty($materials))
                            <div class="col-span-1 grow shrink basis-0 flex-col justify-start items-start gap-6 inline-flex">
                                <div class="flex-col justify-start items-start gap-4 flex">
                                    <div class="text-gray-800 text-2xl font-semibold  leading-[30px]">Materials</div>
                                </div>
                                <div class="self-stretch">
                                    {{-- <ul> --}}
                                        @foreach ($materials as $material)
                                            @if ($material)
                                                <span class="inline-flex items-center rounded-md bg-white  border-gray-300  px-2 py-1 font-medium  ring-1 ring-inset ring-gray-300 mb-1 ">
                                                    {{ $material }}
                                                </span>
                                            @endif
                                        @endforeach
                                    {{-- </ul> --}}
                                </div>
                            </div>
                        @endif
                        @if (!$service->methods->isEmpty())
                            <div class="col-span-1 grow shrink basis-0 flex-col justify-start items-start gap-6 inline-flex">
                                <div class="text-gray-800 text-2xl font-semibold  leading-[30px]">
                                    Methods
                                </div>
                                <div class="self-stretch">
                                    <ul>
                                        @forelse ($service->methods->unique() as $method)
                                            <li>
                                                <a href="/method/{{ $service->id }}/{{ $method->id }}">
                                                    {{ $method->preferred_label }}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="pb-1.5 size-6 w-5 h-6 inline-block">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                    </svg>
                                                </a>
                                            </li>

                                        @empty
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        @endif
                        @if (!$service->tools->isEmpty())
                            <div class="col-span-1 grow shrink basis-0 flex-col justify-start items-start gap-6 inline-flex">
                                <div class="flex-col justify-start items-start gap-4 flex">
                                    <div class="text-gray-800 text-2xl font-semibold  leading-[30px]">
                                        Tools
                                    </div>
                                </div>
                                <div class="self-stretch">
                                    <ul>
                                        @forelse ($service->tools->unique() as $tool)
                                            <li>
                                                <a href="/tool/{{ $service->id }}/{{ $tool->id }}">
                                                    {{ $tool->name }}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="pb-1.5 size-6 w-5 h-6 inline-block">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                    </svg>
                                                </a>

                                            </li>

                                        @empty
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        @endif

                    </div>


                    <div class="grid sm:grid-cols-1  sm:col-span-1 md:col-span-2 self-stretch justify-start items-start gap-14 inline-flex">
                        @if ($service->input_description || $service->output_description)
                            <div class="col-span-1 grow shrink basis-0 flex-col justify-start items-start gap-6 inline-flex">
                                <div class="text-gray-800 text-2xl font-semibold  leading-[30px]">
                                    Other information
                                </div>
                                <div class="self-stretch">
                                    <ul>
                                        @if($service->input_description)
                                        <li>
                                            <div class="text-justify pb-5">
                                                <b>Input</b>: {{ $service->input_description}}
                                            </div>
                                        </li>
                                        @endif
                                        @if($service->output_description)
                                        <li>
                                            <div class="text-justify pb-5">
                                                <b>Output</b>: {{ $service->output_description}}
                                            </div>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        @endif


                    </div>

            </div>
        </div>
    </div>
    <x-filament-actions::modals />
</div>
