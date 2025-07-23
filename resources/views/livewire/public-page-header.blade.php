<div class="w-full h-16 px-8 py-3 bg-white border-b border-gray-200 justify-between items-center inline-flex">
    <a href="{{Route('dashboard')}}">
        <img class="w-28 h-5" src="{{asset('images/erihs_logo.png')}}" />
    </a>

    <div class="justify-end items-center gap-6 flex">
        <div class="justify-end items-center gap-8 flex">

        @if (!$hideCartLink)
            <x-filament::icon-button
                icon="heroicon-o-clipboard-document-check"
                href="{{ Route('cart') }}"
                tag="a">

                <x-slot name="badge">
                    {{ $cartCount}}
                </x-slot>
            </x-filament::icon-button>
        @endif

        @auth
            <x-filament-panels::user-menu />
        @else
            <x-filament::button
                href="{{ Route('login') }}"
                tag="a">
                Sign in
            </x-filament::button>
        @endauth
        </div>
    </div>
</div>
