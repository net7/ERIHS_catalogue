<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum ProposalInternalStatus: string
{
    use EnumHelper;

    case SUBMITTED = 'Submitted';
    case IN_EVALUATION = 'In evaluation';
    case UNDER_REVIEW = 'Under review';
    case CLOSED = 'Closed';
    case ARCHIVED = 'Archived';
}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
