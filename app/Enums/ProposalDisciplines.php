<?php

namespace App\Enums;

use App\Traits\{EnumHelper};


enum ProposalDisciplines: string
{
    use EnumHelper;

    case CONSERVATION_SCIENCE = 'Conservation science';
    case ARCHEOLOGY = 'Archaeology';
    case ANTHROPOLOGY = 'Anthropology';
    case HERITAGE_SCIENCE = 'Heritage science';
    case MATERIAL_SCIENCE = 'Materials science';
    case COMPUTER_SCIENCE = 'Computer science';
    case DIGITAL_HUMANITIES = 'Digital humanities';
    case INFORMATION_MANAGEMENT = 'Information management';
    case DATA_PROCESSING = 'Data processing';
    case DATA_MANAGEMENT = 'Data management';
}
