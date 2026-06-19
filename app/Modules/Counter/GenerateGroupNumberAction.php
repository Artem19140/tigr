<?php

namespace App\Modules\Counter;

use App\Modules\Center\CenterContext;
use App\Enums\CounterKey;
use App\Exceptions\Counter\CounterNotFoundException;
use App\Models\Counter;
use App\Support\CenterIsolationCheck;
use Carbon\Carbon;
use DB;

class GenerateGroupNumberAction
{
    public function __construct(
        protected CenterContext $centerContext
    ) {}

    public function execute(): int
    {
        return DB::transaction(function () {
            $groupNumber = Counter::query()
                ->findLockedOrFail(
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
