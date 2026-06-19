<?php

namespace App\Modules\Counter;

use App\Modules\Center\CenterContext;
use App\Enums\CounterKey;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegNumberGenerator
{
    public function __construct(
        protected CenterContext $centerContext
    ){}

    public function execute():int
    {
        return DB::transaction(function () {
            $regNumber = Counter::findLockedOrFail(
                CounterKey::RegNum,
                $this->centerContext->id()
            );
                
            $this->needReset($regNumber) 
                ?   $regNumber->reset()
                :   $regNumber->increment('value', 1);
            $regNumber->save();

            return $regNumber->value;
        });
    }

    protected function needReset(Counter $counter): bool
    {
        $currentYear = Carbon::now()->year;
        $counterUpdatedYear = $counter->updated_at->year;

        return $counterUpdatedYear !== $currentYear;
    }
}
