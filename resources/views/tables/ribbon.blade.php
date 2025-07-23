{{-- @php


    switch ($getRecord()->ribbonColour()) {
                        case 'yellow':
                            $class = 'yellow-band';
                            break;
                        case 'orange':
                            $class = 'orange-band';
                            break;
                        case 'green':
                            $class = 'green-band';
                            break;
                        case 'red':
                            $class = 'red-band';
                            break;

                        default:
                            $class = '';
                            break;
    }


@endphp --}}

<div @class(['ribbon', $getRecord()->ribbonColour() . '-band'])></div>
