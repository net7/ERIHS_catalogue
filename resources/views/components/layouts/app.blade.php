<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="application-name" content="{{ config('app.name') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}?v={{ date('YmdHis') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->

    <script src="https://cdn.jsdelivr.net/npm/@ryangjchandler/alpine-tooltip@1.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    @filamentStyles




    @vite('resources/css/app.css')

    {{-- Loads montserrat
    @googlefonts('montserrat') --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
</head>

{{-- <body class="font-sans antialiased"> --}}

<body>
    <x-banner />

    {{-- @include('2partials.loading-overlay') --}}

    <!-- @livewire('loading-overlay') -->
    
    <div class="min-h-screen bg-gray-100  mx-auto max-w-screen-2xl shadow-xl">
        {{--   @livewire('navigation-menu') --}}

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif
        <div wire:loading id="loading-overlay-proposal" class="loading-overlay">
            <div class="loading-overlay-image-container">
                <img src="/images/loading.gif" class="loading-overlay-img" />
            </div>
        </div>  
        <!-- Page Content -->
        {{-- <main> --}}
        {{ $slot }}
        {{-- </main> --}}
    </div>

    @stack('modals')

    @livewire('notifications')
    @filamentScripts
    @stack('scripts')
    @vite('resources/js/app.js')
</body>

</html>
