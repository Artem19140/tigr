<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class CountryProvider
{
    protected static string $path = 'countries.json';
    protected static function getPath():string
    {
        if(! Storage::disk('public')->exists(self::$path)){
            Log::warning('Contries json file does not exist');

            abort(500);
        }
        
        return Storage::disk('public')->path(self::$path);
    }
    
    public static function getList()
    {
        return  Cache::rememberForever('countries', function () {
            return collect(
                json_decode(
                    file_get_contents($this->getPath()),
                    true
                )
            );
        });
    }
}