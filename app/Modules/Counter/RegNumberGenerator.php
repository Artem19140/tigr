<?php

namespace App\Modules\Counter;

use App\Enums\CounterKey;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegNumberGenerator
{
    public function execute(int $centerId):int
    {
        return DB::transaction(function () use($centerId) {
            $regNumber = Counter::findLockedOrFail(
                CounterKey::RegNum,
                $centerId
            );
                
            $this->needReset($regNumber) 
                ?   $regNumber->reset()
                :   $regNumber->increment('value', 1);
            $regNumber->save();

            return $regNumber->value;
        });
    }

    protected function needReset(Counter $counter): bool
    {
        $currentYear = Carbon::now()->year;
        $counterUpdatedYear = $counter->updated_at->year;

        return $counterUpdatedYear !== $currentYear;
    }
}
