<?php

namespace App\Enums;

use App\Traits\{EnumHelper};


enum LearnedAboutErihs: string
{
    use EnumHelper;

    case PERSONAL_CONTACT = 'Personal contact';
    case WEB_PAGE = 'Web page';
    case CONFERENCE = 'Conference';
    case ARTICLE = 'Article';
    case OTHER = 'Other';


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
