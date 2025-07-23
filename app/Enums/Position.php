<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum Position: string
{
    use EnumHelper;

    case UNDERGRADUATE = 'Undergraduate';
    case POSTGRAD = 'Post graduate';
    case POSTDOCREAS = 'Post-doc researcher';
    case TECHNICIAN = 'Technician';
    case EXPREAS = 'Experienced researcher';
}
