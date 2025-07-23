<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum ProposalType: string
{
    use EnumHelper;

    case NEW = 'New';
    case LONG_TERM_PROJECT = 'Long-term project';
    case RESUBMISSION = 'Resubmission';

}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
