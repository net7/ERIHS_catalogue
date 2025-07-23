<?php

namespace App\Enums;

use App\Traits\{EnumHelper};


enum ProposalSocialChallenges: string
{
    use EnumHelper;

    case HEALTH = 'Health';
    case CULTURE_CREATIVITY_INCLUSIVE_SOCIETY = 'Culture, creativity and inclusive society';
    case CIVIL_SECURITY_FOR_SOCIETY = 'Civil security for society';
    case DIGITAL_INDUSTRY_AND_SPACE = 'Digital, industry and space';
    case CLIMATE_ENERGY_AND_MOBILITY = 'Climate, energy and mobility';
    case SUSTAINABLE_FOOD = 'Food, bioeconomy, natural resources, agricultural and environment';

    // Metodo per recuperare un'istanza dell'enum dal nome del case
    public static function fromName(string $name): string
    {
        foreach (self::cases() as $status) {
            if( $name === $status->name ){
                return $status->value;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class );
    }
}
