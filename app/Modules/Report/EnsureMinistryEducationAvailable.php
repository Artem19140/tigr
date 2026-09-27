<?php

namespace App\Modules\Report;

use App\Exceptions\BusinessException;
use App\Models\Attempt;
use Carbon\Carbon;

class EnsureMinistryEducationAvailable
{
    public function execute(
        Carbon $dateFrom,
        Carbon $dateTo
    ):void
    {
        $hasNoData = ! Attempt::query()
            ->whereBetween('created_at', [
                $dateFrom,
                $dateTo,
            ])
            ->exists();
        $period = "с {$dateFrom->copy()->format('d.m.Y')} по {$dateTo->copy()->format('d.m.Y')}";
        if($hasNoData){
            throw new BusinessException("Данных для отчета $period нету");
        }
    }
}