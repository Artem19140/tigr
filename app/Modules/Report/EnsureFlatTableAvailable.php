<?php

namespace App\Modules\Report;

use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;

class EnsureFlatTableAvailable
{
    public function execute(
        Carbon $dateFrom,
        Carbon $dateTo
    ): void 
    {
        $hasNoData = ! Attempt::query()
            ->whereBetween('created_at', [
                $dateFrom->copy()->setTimezone(CenterData::timeZome())->startOfDay()->utc(),
                $dateTo->copy()->setTimezone(CenterData::timeZome())->endOfDay()->utc(),
            ])

            ->whereNotNull('reviewed_at')
            ->exists();
        if($hasNoData){
            throw new BusinessException('Нет данных для выгрузки');
        }
    }
    
}