<div
    class="sm:col-span-1
            @if (isset($short) && $short)
            md:col-span-2
            @else
            md:col-span-4
            @endif
            flex-col justify-start items-start gap-1 inline-flex mt-10 mr-10 pb-10 @if (!isset($last) || !$last) border-b-2 @endif">
    <div class="text-gray-700 text-xl font-bold font-['Montserrat'] leading-normal">
        {{ $title }}
    </div>
    <div class="self-stretch text-gray-800 text-base font-normal font-['Montserrat'] leading-normal">
        @if (is_a($data, 'Illuminate\Database\Eloquent\Collection'))
            @foreach ($data as $item)
                @if (!isset($plain) || !$plain)
                    <span
                        class="inline-flex items-center rounded-md bg-white  border-gray-300  px-2 py-1 font-medium  ring-1 ring-inset ring-gray-300 mb-2 mr-1">
                @endif
                {{ $item->name }} <br/>
                @if (!isset($plain) || !$plain)
                    </span>
                @endif
            @endforeach
        @elseif (is_array($data))
            @foreach ($data as $item)
                @if (!isset($plain) || !$plain)
                    <span
                        class="inline-flex items-center rounded-md bg-white  border-gray-300  px-2 py-1 font-medium  ring-1 ring-inset ring-gray-300 mb-2 mr-1">
                @endif {{ $item }}  <br/> 
                @if (!isset($plain) || !$plain)
                    </span>
                @endif
            @endforeach
        @else
            {{ $data }}
        @endif

    </div>
</div>
