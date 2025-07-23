<?php

namespace App\Enums;

use App\Traits\{EnumHelper};


enum ProposalReviewerRefusalReason: string
{
    use EnumHelper;

    case CONFLICT_OF_INTEREST = 'Conflict of interest';
    case TIMED_OUT = 'Timed out';
    case EXPLICIT_REFUSAL = 'Explicit refusal';
}
