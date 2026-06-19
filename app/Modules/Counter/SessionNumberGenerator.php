<?php

namespace App\Modules\Counter;

use App\Enums\CounterKey;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SessionNumberGenerator
{

    public function execute(int $centerId):int
    {
        return DB::transaction(function () use($centerId) {
            $sessionCounter = Counter::findLockedOrFail(
                CounterKey::Session, 
                $centerId
            );

            $this->needReset($sessionCounter)
                ?   $sessionCounter->reset()
                :   $sessionCounter->increment('value', 1);
            $sessionCounter->save();

            return $sessionCounter->value;
        });
    }

    protected function needReset(Counter $counter): bool
    {
        $currentYear = Carbon::now()->year;
        $counterUpdatedYear = $counter->updated_at->year;

        return $counterUpdatedYear !== $currentYear;
    }
}
