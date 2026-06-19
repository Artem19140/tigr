<?php

namespace App\Enums;

use App\Exceptions\Counter\CounterNotFoundException;
use Illuminate\Support\Carbon;

enum CounterKey: string
{
    case RegNum = 'reg_num';
    case Group = 'group';
    case Session = 'session';

    public function defaultValue(){
        return match($this){
            self::RegNum => \intval(Carbon::now()->format('y') . '0000'),
            self::Group => 1,
            self::Session => 1,
            default => throw new CounterNotFoundException($this)
        };
    }
}
