<?php

namespace App\Enums;

use App\Traits\{EnumHelper};


enum ProposalReviewerStatus: string
{
    use EnumHelper;

    case TO_BE_CONFIRMED = 'To be confirmed';
    case ACCEPTED = 'Accepted';
    case REFUSED = 'Refused';
    case WAITING = 'Waiting';
    case SKIPPED = 'Skipped';
}
