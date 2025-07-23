<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum ProposalOwnershipConsent: string
{
    use EnumHelper;

    case REQUESTED = 'Requested';
    case RECEIVED = 'Received';
    case OTHER = 'Other';
}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
