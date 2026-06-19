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

            $this->needReset($groupNumber) 
                ?  $groupNumber->reset() 
                :  $groupNumber->increment('value', 1);
            return $groupNumber->value;
        });
    }

    protected function needReset(Counter $counter): bool
    {
        $today = Carbon::now()->toDateString();
        $counterUpdatedAt = $counter->updated_at->toDateString();

        return $counterUpdatedAt !== $today;
    }
}
