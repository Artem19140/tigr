<?php

namespace App\Modules\Counter;

use App\Modules\Center\CenterContext;
use App\Enums\CounterKey;
use App\Exceptions\Counter\CounterNotFoundException;
use App\Models\Counter;
use App\Support\CenterIsolationCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class GenerateRegNumberAction
{
    public function execute(): int
    {
        return DB::transaction(function () {
            $regNumber = $this->findOrFailRegNumCounter();
            if ($this->isNewYear($regNumber)) {
                $regNumber->value = \intval(Carbon::now()->format('y').'0000');
                $regNumber->updated_at = Carbon::now();
            }
            $regNumber->value += 1;
            $regNumber->save();

            return $regNumber->value;
        });
    }

    protected function findOrFailRegNumCounter()
    {
        $regNumber = Counter::query()
            ->forCenter(app(CenterContext::class)->id())
            ->where('key', CounterKey::RegNum)
            ->lockForUpdate()
            ->first();
        CenterIsolationCheck::centerBelongs($regNumber, app(CenterContext::class)->id());
            if (! $regNumber) {
                throw new CounterNotFoundException(CounterKey::RegNum);
            }
        return $regNumber;
    }

    protected function isNewYear(Counter $regNumber): bool
    {
        return $regNumber->updated_at->year !== Carbon::now()->year;
    }
}
