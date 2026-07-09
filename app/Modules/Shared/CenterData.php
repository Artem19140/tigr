<?php

namespace App\Modules\Shared;

class CenterData
{
    public static function ogrn():string
    {
        return config('center.ogrn');
    }

    public static function inn():string
    {
        return config('center.inn');
    }

    public static function name():string
    {
        return config('center.name');
    }

    public static function shortName():string
    {
        return config('center.short_name');
    }

    public static function timeZome():string
    {
        return config('center.time_zome');
    }

    public static function commissionChairman():string
    {
        return config('center.commission_chairman');
    }

    public static function certificatesIssueAddress():string
    {
        return config('center.certificates_issue_address');
    }

    public static function nameGenitive():string
    {
        return config('center.name_genitive');
    }

    public static function address():string
    {
        return config('center.address');
    }

    public static function directorFio():string
    {
        return config('center.director_fio');
    }
}