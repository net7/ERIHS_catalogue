<?php


namespace App\Services;

use Illuminate\Support\Facades\Auth;

class ERIHSFavouriteService extends ERIHSCommonDBCartService
{
    public static function getCartName(): string
    {
        return 'favourites';
    }


    public static function addItem($toolId): void
    {
        if (Auth::check()) {
            parent::addItem($toolId);
        }
    }

    public static function removeItem($toolId): void
    {
        if (Auth::check()) {
            parent::removeItem($toolId);
        }
    }
}
