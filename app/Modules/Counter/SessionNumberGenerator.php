<?php

namespace App\Modules\Counter;

use App\Enums\CounterKey;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SessionNumberGenerator
{

    public function execute():int
    {
        return DB::transaction(function () {
            $sessionCounter = Counter::findLockedOrFail(
                CounterKey::Session,
            );

            if($sessionCounter->notInitialized()){ 
                $sessionCounter->initialize();
                $sessionCounter->save();
                return $sessionCounter->value;
            }

            $this->shouldReset($sessionCounter)
                ?   $sessionCounter->reset()
                :   $this->incrementIfNeeded($sessionCounter);
                
            $sessionCounter->save();

            return $sessionCounter->value;
        });
    }

    protected function shouldReset(Counter $counter): bool
    {
        $currentYear = Carbon::now()->year;
        $counterLastIncrementYear = $counter->last_increment_at->year;

        return $counterLastIncrementYear !== $currentYear;
    }

    protected function incrementIfNeeded(Counter $counter): void
    {
        $today = Carbon::now()->toDateString();
        $counterUpdatedDay = $counter->last_increment_at->toDateString();

        if($today === $counterUpdatedDay){
            return ;
        }

        $counter->incrementValue();
    }
}
