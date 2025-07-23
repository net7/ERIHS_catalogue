<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<div
    class="border-solid border-[#e5e7eb] bg-[#f9fafb] self-stretch flex flex-col justify-center pl-8 h-16 shrink-0 items-start border-t-0 border-b border-x-0 fixed w-full top-0">
    <img src="https://file.rendit.io/n/4X0BRij0hTxEHgcTwPeD.png" class="min-h-0 min-w-0" />
</div>

<body class="flex flex-col items-center justify-center h-screen bg-white">
    <div class="w-full p-6 mb-4 bg-white rounded-xl border border-gray-300 flex-col justify-start gap-4 inline-flex">
        <div class="self-stretch justify-start items-start gap-16 inline-flex">
            <div class="grow shrink basis-0 h-6 justify-start items-start gap-2 flex">
                <div class="grow shrink basis-0 text-gray-800 text-lg font-bold font-['Montserrat'] leading-relaxed">
                    {{ $service->title }}
                </div>
            </div>
        </div>
        <div class="self-stretch h-px flex-col justify-center items-start flex">
            <div class="w-full h-px relative bg-gray-200"></div>
        </div>
        <div class="self-stretch text-neutral-900 text-sm font-normal font-['Montserrat'] leading-tight">
            {{ $service->description }}
        </div>
        <div class="self-stretch text-neutral-900 text-sm font-normal font-['Montserrat'] leading-tight">
            {{ $service->organization->name }}
            {{ $service->organization->mbox }}
            <h2>Countries:</h2>
            <ul>
                @forelse ($countries as $tag)
                    <li>{{ $tag->name }}</li>
                @empty
                    <li>Nessuna nazione trovata.</li>
                @endforelse
            </ul>


        </div>

        <!--<div class="self-stretch justify-between items-end inline-flex">
        <div class="w-full flex-col justify-start items-start gap-2 inline-flex">
            <div class="text-gray-600 text-sm font-normal font-['Montserrat'] leading-tight">Organization</div>
            <div class="justify-start items-center gap-2 inline-flex">
                <div class="text-slate-600 text-sm font-semibold font-['Montserrat'] leading-tight">
                     {{-- {{ $tool->organization->name }} --}}
                </div>
                <div class="w-5 h-px  gap-1 rotate-90 border border-gray-300"></div>
                <div class="justify-start items-center gap-2 flex">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>

                    </div>
                    <div class="text-slate-600 text-sm font-semibold font-['Montserrat'] leading-tight">
                       {{-- {{ $tool->organization->state }} --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="h-8 opacity-0 justify-start items-start gap-2.5 flex">
            <div class="w-8 h-8 p-1 justify-center items-center flex"></div>
        </div>
    </div>-->
    </div>
</body>
