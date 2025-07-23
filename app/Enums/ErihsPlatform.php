<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum ErihsPlatform: string
{
    use EnumHelper;

    case ARCHLAB = 'archlab';
    case DIGILAB = 'digilab';
    case FIXLAB = 'fixlab';
    case MOLAB = 'molab';
}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
