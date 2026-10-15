<?php

namespace App\Modules\Counter;
use App\Enums\CounterKey;
use App\Models\Counter;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GroupNumberGenerator
{
    public function execute(bool $rollback = false): int
    {
        return DB::transaction(function () use( $rollback ) {
            $groupNumber = Counter::findLockedOrFail(
                CounterKey::Group
            );

            if($groupNumber->notInitialized()){
                $groupNumber->initialize();
                $groupNumber->save();

                if($rollback){
                    return $this->rollback($groupNumber->value);
                }

                return $groupNumber->value;
            }

            $this->shouldReset($groupNumber) 
                ?  $groupNumber->reset() 
                :  $groupNumber->incrementValue();
            $groupNumber->save(); 

            if($rollback){
                return $this->rollback($groupNumber->value);
            }

            return $groupNumber->value;
            
        });
    }

    protected function shouldReset(Counter $counter): bool
    {
        $timeZone = CenterData::timeZome();
        $today = Carbon::now()
            ->setTimezone($timeZone)
            ->toDateString();

        $counterUpdatedAt = $counter->last_increment_at
            ->setTimezone($timeZone)
            ->toDateString();

        return $counterUpdatedAt !== $today;
    }

    protected function rollback(int $value)
    {
        DB::rollBack();
        return $value;
    }
}
