<div class="self-stretch flex-col justify-start items-start gap-4 flex pb-2">
    <div class="self-stretch justify-start items-start gap-2 inline-flex">
        <div class="justify-start items-start gap-2 flex @if (isset($titlePadding) && $titlePadding) pt-1.5 @endif">
            <div class="w-4 relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                </svg>
            </div>
            <div class="text-gray-700 text-sm font-semibold font-['Montserrat'] leading-tight">
                {{ $title }}:
            </div>
        </div>

        @if ($bold)
            @php $textStyle = 'text-gray-800 font-bold'; @endphp
        @else
            @php $textStyle = 'text-gray-600 font-normal'; @endphp
        @endif
       
        <div class="grow shrink basis-0 {{ $textStyle }} text-sm  font-['Montserrat'] leading-tight flex flex-wrap  max-w-full">
            {!! $text !!}
        </div>
    </div>

</div>
