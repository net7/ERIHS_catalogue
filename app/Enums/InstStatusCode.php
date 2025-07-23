<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum InstStatusCode: string
{
    use EnumHelper;

    case UNIVERSITY = 'University';
    case RESEARCHORG = 'Public research organization';
    case ENTERPRISE = 'Small or medium enterprise';
    case ORGANIZATION = 'Other and/or profit or non profit private organization';
    case OTHERORG = 'Other organization';
}
