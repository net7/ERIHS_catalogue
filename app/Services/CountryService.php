<?php

namespace App\Services;
use App\Models\Country;

class CountryService
{
    public static function getCountries()
    {
        $countries = Country::all();
        return $countries;
    }
}
