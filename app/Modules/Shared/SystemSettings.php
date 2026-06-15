<?php

namespace App\Modules\Shared;

class SystemSettings{
    public static function codesLength():int
    {
        return config('system.codes_length');
    }

    public static function codesTtl():int
    {
        return config('system.codes_ttl');
    }

    public static function d():int
    {
        return config('system.codes_ttl');
    }

}