<?php

namespace App\Modules\Counter;

use App\Enums\CounterKey;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SessionNumberGenerator
{

    public function execute(bool $rollback = false):int
    {
        return DB::transaction(function () use( $rollback ) {
            $sessionCounter = Counter::findLockedOrFail(
                CounterKey::Session,
            );

            if($sessionCounter->notInitialized()){ 
                $sessionCounter->initialize();
                $sessionCounter->save();

                if($rollback){
                    return $this->rollback($sessionCounter->value);
                }

                return $sessionCounter->value;
            }

            $this->shouldReset($sessionCounter)
                ?   $sessionCounter->reset()
                :   $this->incrementIfNeeded($sessionCounter);
                
            $sessionCounter->save();

            if($rollback){
                return $this->rollback($sessionCounter->value);
            }

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

    protected function rollback(int $value)
    {
        DB::rollBack();
        return $value;
    }
}
