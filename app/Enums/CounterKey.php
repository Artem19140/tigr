<?php

namespace App\Enums;

use App\Exceptions\Counter\CounterNotFoundException;
use Illuminate\Support\Carbon;

enum CounterKey: string
{
    case RegNum = 'reg_num';
    case Group = 'group';

    public static function defaultValue(self $key){
        return match($key){
            self::RegNum => \intval(Carbon::now()->format('y') . '0000'),
            self::Group => 0,
            default => throw new CounterNotFoundException($key)
        };
    }
}
