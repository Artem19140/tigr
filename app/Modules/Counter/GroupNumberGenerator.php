<?php

namespace App\Modules\Counter;
use App\Enums\CounterKey;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GroupNumberGenerator
{

    public function execute(int $centerId): int
    {
        return DB::transaction(function () use($centerId) {
            $groupNumber = Counter::findLockedOrFail(
                CounterKey::Group, 
                $centerId
            );

            if($groupNumber->notInitialized()){
                $groupNumber->initialize();
                $groupNumber->save();
                return $groupNumber->value;
            }

            $this->shouldReset($groupNumber) 
                ?  $groupNumber->reset() 
                :  $groupNumber->incrementValue();
                
            return $groupNumber->value;
        });
    }

    protected function shouldReset(Counter $counter): bool
    {
        $today = Carbon::now()->toDateString();
        $counterUpdatedAt = $counter->last_increment_at->toDateString();

        return $counterUpdatedAt !== $today;
    }
}
