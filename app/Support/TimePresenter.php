<?php

namespace App\Support;

use App\Models\Center;
use Carbon\Carbon;

class TimePresenter
{
    public static function forCenter(
        ?Carbon $date,
        ?Center $center
    ): ?Carbon {
        if (! $date || ! $center) {
            return null;
        }

        return $date->copy()->setTimezone($center->time_zone);
    }
}
