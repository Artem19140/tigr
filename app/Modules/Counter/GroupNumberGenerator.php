<?php

namespace App\Modules\Counter;

use App\Modules\Center\CenterContext;
use App\Enums\CounterKey;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GroupNumberGenerator
{
    public function __construct(
        protected CenterContext $centerContext
    ) {}

    public function execute(): int
    {
        return DB::transaction(function () {
            $groupNumber = Counter::findLockedOrFail(
                CounterKey::Group, 
                $this->centerContext->id()
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
