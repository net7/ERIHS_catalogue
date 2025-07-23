<?php

namespace App\Enums;

use App\Traits\{EnumHelper};


enum ProposalConnection: string
{
    use EnumHelper;

    case PERSONAL_CONTACT = 'Personal contact';
    case WEB_PAGE = 'Web page';
    case CONFERCENC = 'Conference';
    case ARTICLE = 'Article';
    case OTHER = 'Other';
}
