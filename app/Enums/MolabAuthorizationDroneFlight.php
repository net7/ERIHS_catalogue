<?php

namespace App\Enums;

use App\Traits\{EnumHelper};


enum MolabAuthorizationDroneFlight: string
{
    use EnumHelper;

    case REQUESTED = 'Requested';
    case RECEIVED = 'Received';
    case OTHER = 'Other';
    case NON_APPLICABLE = 'Non applicable';


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
