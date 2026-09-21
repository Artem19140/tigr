<?php

namespace App\Modules\Counter;

use App\Enums\CounterKey;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegNumberGenerator
{
    public function execute(bool $rollback = false):int
    {
        return DB::transaction(function () use( $rollback ) {
            $regNumber = Counter::findLockedOrFail(
                CounterKey::RegNum
            );
    
            if($regNumber->notInitialized()){ 
                $regNumber->initialize();
                $regNumber->save();

                if($rollback){
                    return $this->rollback($regNumber->value);
                }

                return $regNumber->value;
            }

            $this->shouldReset($regNumber) 
                ?   $regNumber->reset()
                :   $regNumber->incrementValue();

            $regNumber->save();
            
            if($rollback){
                return $this->rollback($regNumber->value);
            }

            return $regNumber->value;
        });
    }

    protected function shouldReset(Counter $counter): bool
    {
        $currentYear = Carbon::now()->year;
        $counterLastIncrementYear = $counter->last_increment_at->year;

        return $counterLastIncrementYear !== $currentYear;
    }

    protected function rollback(int $value)
    {
        DB::rollBack();
        return $value;
    }
}
