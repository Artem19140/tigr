<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class CountryProvider
{
    public static function getList()
    {
        return  Cache::rememberForever('countries', function () {
            return collect(
                json_decode(
                    file_get_contents(storage_path('app/public/countries.json')),
                    true
                )
            );
        });
    }
    
}