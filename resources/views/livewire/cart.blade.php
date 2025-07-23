<div>
    @livewire('public-page-header', ['hideCartLink' => true])

    <div class=" mb-10 mx-auto border-solid px-5 pb-5">
        <div class="h-56 pt-8 px-8 pb-8 mb-10 rounded-xl justify-between items-end inline-flex">
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
                <div class="text-gray-900 text-3xl font-bold font-['Montserrat'] leading-10">Confirm services</div>
                <div class="self-stretch text-gray-800 text-base font-normal font-['Montserrat'] leading-normal">
                    Please note that only one proposal can be submitted at a time as a User Group Leader.
                    Ensure that all services you intend to request are included with this proposal.
                    If the proposal is still in draft, you must complete and submit it before starting a new one.</div>
            </div>
            <div class="justify-start items-start gap-4 flex">
                <div
                    class="px-4 py-2.5 bg-gray-50 rounded-lg shadow border border-gray-700 justify-center items-center gap-1.5 flex">
                    <div class="text-center text-gray-800 text-sm font-semibold font-['Montserrat'] leading-tight">
                        <a href="{{ Route('catalogue') }}">
                            Add more services
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6"
            style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(4, minmax(0, 1fr));">


            <div class="col-[--col-span-default] lg:col-[--col-span-lg]"
                style="--col-span-default: span 1 / span 1; --col-span-lg: span 3 / span 3;">

                {{ $this->table }}
            </div>

            {{--  <div class=" px-6 pb-4 bg-white rounded-xl border border-gray-300 flex-col justify-start items-start gap-4 inline-flex"> --}}

            {{-- <div class=" px-6 pb-4 bg-white rounded-xl ">        --}}
            {{-- <div class="w-11 h-5 bg-red-600"></div> --}}
            {{-- {{ $this->cartInfolist }} --}}
            {{-- </div> --}}


            <div class="relative">
                {{ $this->cartInfolist }}
            </div>
        </div>
    </div>

</div>
