<?php

namespace App\Enums;

use App\Traits\{EnumHelper};


enum ProposalEligibility: string
{
    use EnumHelper;

    case NATIONALITY = 'Nationality';
    case RESEARCH_NOVELTY = 'Research novelty';
    case OTHER = 'other';
}
