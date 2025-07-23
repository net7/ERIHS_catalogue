<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum Gender: string
{
    use EnumHelper;

    case MALE = 'Male';
    case FEMALE = 'Female';
    case OTHER = 'Other';
    case ND = 'Prefer not to say';

}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
