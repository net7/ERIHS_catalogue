<?php


namespace App\Services;

use Illuminate\Support\Facades\Auth;

class ERIHSAuthService
{
    public static function checkLogged(): bool
    {
        if (Auth::check()) {
            return true;
        }
        return false;
    }
}
