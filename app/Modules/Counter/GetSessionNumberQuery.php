<?php

namespace App\Modules\Counter;

use App\Enums\CounterKey;
use App\Models\Counter;
use App\Modules\Center\CenterContext;
use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GetSessionNumberQuery
{
    public function __construct(
        protected CenterContext $centerContext
    ) {}
    public function execute(Carbon $beginTime): int
    {
        $sessionNumber = Exam::query()
            ->forCenter(app(CenterContext::class)->id())
            ->whereBetween('begin_time', [
                $beginTime->copy()->startOfYear(),
                $beginTime->copy()->subDay()->endOfDay(),
            ])
            ->notCancelled()
            ->whereHas('attempts')
            ->get()
            ->groupBy(function ($exam) {
                return $exam->begin_time->copy()->toDateString();
            })
            ->count();

        return $sessionNumber + 1;
    }

    public function execute1():int
    {
        $sessionCounter = Counter::query()
            ->findLockedOrFail(CounterKey::Session, $this->centerContext->id());

        $this->resetIfNeeded($sessionCounter);

        $today = Carbon::now();
        
        if($sessionCounter->updated_at !== $today){
            $sessionCounter->increment('value', 1);
            $sessionCounter->save();
        }
        return $sessionCounter->value;
    }

    protected function resetIfNeeded(Counter $counter)
    {
        $currentYear = Carbon::now()->year;
        $counterUpdatedYear = $counter->updated_at->copy()->year;

        if($currentYear !== $counterUpdatedYear){
            $counter->reset();
            $counter->save();
        }
    }
}
