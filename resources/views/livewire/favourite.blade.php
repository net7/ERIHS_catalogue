<div >
    @livewire('public-page-header')

    <div class=" mb-10 mx-auto border-solid px-5 pb-5">
        {{-- <div class="h-64 px-8 pt-8 pb-16 mb-10 rounded-xl justify-between items-end inline-flex"> --}}
        <div class="h-40 px-8 pt-8 pb-16 mb-10 rounded-xl justify-between items-end ">
            {{-- <div class="w-4/6 flex-col justify-start items-start gap-2 inline-flex"> --}}

            <div class="flex w-6/12 flex-col items-start gap-2 opacity-[var(--Notification-number,1)]">
                <div class="justify-start items-center gap-0.5 inline-flex">

                    <div class="w-4 h-4">
                        <a href="{{ Route('catalogue') }}">
                            <svg class="shrink-0 w-4 h-4 relative overflow-visible" style="" width="16"
                                height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.14645 8.35355C4.95118 8.15829 4.95118 7.84171 5.14645 7.64645L10.1464 2.64645C10.3417 2.45118 10.6583 2.45118 10.8536 2.64645C11.0488 2.84171 11.0488 3.15829 10.8536 3.35355L6.20711 8L10.8536 12.6464C11.0488 12.8417 11.0488 13.1583 10.8536 13.3536C10.6583 13.5488 10.3417 13.5488 10.1464 13.3536L5.14645 8.35355Z"
                                    fill="#44676A" />
                            </svg>
                        </a>
                    </div>
                    <div class="text-slate-600 text-base font-normal font-['Montserrat'] leading-normal">
                        <a href="{{ Route('catalogue') }}">
                            Go back to catalogue
                        </a>
                    </div>

                </div>
                <div class="text-gray-900 text-3xl font-bold font-['Montserrat'] leading-10">Favourites</div>
                <div class="self-stretch text-gray-800 text-base font-normal font-['Montserrat'] leading-normal">
                    Here you can find all the services you have saved for later.</div>
            </div>

        </div>



        <div class="col-[--col-span-default] lg:col-[--col-span-lg]"
            style="--col-span-default: span 1 / span 1; --col-span-lg: span 3 / span 3;">

            {{ $this->table }}
        </div>

    </div>
        {{-- <div class="w-full lg:col-span-2 xl:col-span-3 p-4">
            @foreach ($tools as $tool)
                @livewire('tool-box', ['tool' => $tool], key('tool-' . $tool->id))
            @endforeach
        </div> --}}
    {{-- </div>  --}}
</div>
