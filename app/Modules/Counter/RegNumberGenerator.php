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
        return DB::transaction(function () use ( $centerId ) 
        {
            $regNumber = Counter::findLockedOrFail(
                CounterKey::RegNum,
                $centerId
            );
    
            if($regNumber->notInitialized()){ 
                $regNumber->initialize();
                $regNumber->save();
                return $regNumber->value;
            }

            $this->shouldReset($regNumber) 
                ?   $regNumber->reset()
                :   $regNumber->incrementValue();

            $regNumber->save();

            return $regNumber->value;
        });
    }

    protected function shouldReset(Counter $counter): bool
    {
        $currentYear = Carbon::now()->year;
        $counterLastIncrementYear = $counter->last_increment_at->year;

        return $counterLastIncrementYear !== $currentYear;
    }
}
