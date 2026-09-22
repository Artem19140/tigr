<?php

namespace App\Modules\Shared;

use App\Models\Center;
use Illuminate\Support\Facades\Cache;

class CenterData
{
    private static Center | null $center = null;

    private static function get(): Center
    {
        if (self::$center === null) {
            self::$center = Cache::rememberForever(
                Center::CACHE_KEY,
                fn () => Center::firstOrFail()
            );
        }

        return self::$center;
    }
    public static function ogrn():string
    {
        return self::get()->ogrn;
    }

    public static function inn():string
    {
        return self::get()->inn;
    }

    public static function name():string
    {
        return self::get()->name;
    }

    public static function shortName():string
    {
        return self::get()->short_name;
    }

    public static function timeZome():string
    {
        return config('center.time_zome');
    }

    public static function commissionChairman():string
    {
        return self::get()->commission_chairman;
    }

    public static function certificatesIssueAddress():string
    {
        return self::get()->certificates_issue_address;
    }

    public static function nameGenitive():string
    {
        return self::get()->name_genitive;
    }

    public static function address():string
    {
        return self::get()->address;
    }

    public static function directorFio():string
    {
        return self::get()->director_fio;
    }
}