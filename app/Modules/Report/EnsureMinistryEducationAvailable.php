<?php

namespace App\Modules\Report;

use App\Enums\BusinessCode;
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

        if($hasNoData){
            throw new BusinessException(
                BusinessCode::NoData
            );
        }
    }
}