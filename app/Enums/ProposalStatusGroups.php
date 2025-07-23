<?php

namespace App\Enums;

use App\Traits\EnumHelper;
use Gecche\FSM\Contracts\FSMConfigInterface;

enum ProposalStatusGroups: string
{
    use EnumHelper;


    case DELETABLE = 'deletable';
    case DRAFT = 'draft';
    case YELLOW = 'yellow';
    case GREEN = 'green';
    case RED = 'red';
    case GRAY = 'gray';
    case IN_FEASIBILITY = 'in_feasibility';
    case REVIEWABLE = 'reviewable';
    case CAN_BE_EDITED_BY_HELP_DESK = 'can_be_edited_by_help_desk';
    case CAN_BE_DISCARDED = 'can_be_discarded';
    case CAN_BE_CONFIRMED = 'can_be_confirmed';
    case FINAL = 'final';

}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
