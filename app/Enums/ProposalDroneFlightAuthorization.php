<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum ProposalDroneFlightAuthorization: string
{
    use EnumHelper;

    case REQUESTED = 'Requested';
    case RECEIVED = 'Received';
    case NON_APPLICABLE = 'Non applicable';
    case OTHER = 'Other';

}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
