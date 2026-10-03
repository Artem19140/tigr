<?php

namespace App\Modules\Report;

use App\Enums\BusinessCode;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;

class EnsureFlatTableAvailable
{
    public function execute(
        Carbon $dateFrom,
        Carbon $dateTo
    ): void {
        $period = $this->getPeriod(
            $dateFrom,
            $dateTo
        );

        if($this->hasNoDataFor($period)){
            throw new BusinessException(
                BusinessCode::NoData
            );
        }
    }
    
    protected function getPeriod(
        Carbon $dateFrom,
        Carbon $dateTo
    ): array
    {
        $timeZone = CenterData::timeZome();

        $begin = $dateFrom->copy()->setTimezone(
            $timeZone
        )->startOfDay()->utc();
        
        $end = $dateTo->copy()->setTimezone(
            $timeZone
        )->endOfDay()->utc();

        return [
            $begin,
            $end
        ];
    }

    protected function hasNoDataFor(array $period): bool
    {
        return ! Attempt::query()
            ->whereBetween('created_at', $period)
            ->whereNotNull('reviewed_at')
            ->exists();
    }
}