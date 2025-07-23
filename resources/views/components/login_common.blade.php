<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}?v={{ date('YmdHis') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body class="bg-white">
    <div class="flex flex-col items-center">

        <!-- Top Navbar -->
        <div
            class="border-solid border-[#e5e7eb] bg-[#f9fafb] w-full h-16 flex items-center justify-start border-t-0 border-b border-x-0 px-4">
            <a href="{{ route('catalogue') }}">
                <img src="{{ url('/images/erihs_logo.png') }}" class="min-h-0 min-w-0">
            </a>
        </div>

        <div class="flex flex-col md:flex-row w-full items-center justify-center">
            <!-- Left Div -->
            <div class="w-full md:w-1/2 bg-[#223435] relative flex items-center justify-center">
                <div
                    class="bg-erihs-bg bg-cover bg-50%_50% bg-blend-normal bg-no-repeat flex flex-col gap-4 h-[857px] md:h-[1024px] px-8 py-20">
                    <div class="flex flex-col items-start justify-center h-full">
                        <div class="text-4xl font-['Montserrat'] font-bold leading-[56px] text-[#f9fafb] ml-1">
                            Welcome to the Catalogue of Services
                        </div>
                        <div
                            class="text-xl font-['Montserrat'] font-semibold tracking-[-0.4] leading-[28px] text-[#f9fafb] text-left">
                            Sign in to access your account
                        </div>
                    </div>
                </div>
            </div>

            {{ $slot }}

        </div>
    </div>
</body>

</html>
