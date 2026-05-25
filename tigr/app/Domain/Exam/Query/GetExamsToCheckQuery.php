<?php

namespace App\Domain\Exam\Query;

use App\Models\Employee;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class GetExamsToCheckQuery
{
    public function execute(Employee $employee): Collection
    {
        return Exam::query()
            ->with(['type'])
            ->visibleFor($employee)
            ->whereHas('type', function (Builder $query) {
                $query->where('need_human_check', true);
            })
            ->whereHas('attempts', function (Builder $query) {
                $query->unchecked();
            })
            ->get();
    }
}
