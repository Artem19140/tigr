<?php

namespace App\Modules\Counter;

use App\Enums\CounterKey;
use App\Models\Counter;
use App\Modules\Center\CenterContext;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SessionNumberGenerator
{
    public function __construct(
        protected CenterContext $centerContext
    ) {}

    public function execute():int
    {
        return DB::transaction(function () {
            $sessionCounter = Counter::findLockedOrFail(
                CounterKey::Session, 
                $this->centerContext->id()
            );

            $this->needReset($sessionCounter)
                ?   $sessionCounter->reset()
                :   $sessionCounter->increment('value', 1);
            $sessionCounter->save();

            return $sessionCounter->value;
        });
    }

    protected function needReset(Counter $counter): bool
    {
        $currentYear = Carbon::now()->year;
        $counterUpdatedYear = $counter->updated_at->year;

        return $counterUpdatedYear !== $currentYear;
    }
}
