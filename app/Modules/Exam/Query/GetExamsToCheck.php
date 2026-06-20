<?php

namespace App\Modules\Exam\Query;

use App\Models\Employee;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class GetExamsToCheck
{
    public function execute(Employee $employee): Collection
    {
        return Exam::query()
            ->with(['type', 'center'])
            ->examiner($employee)
            ->whereHas('type', function (Builder $query) {
                $query->where('need_human_check', true);
            })
            ->whereHas('attempts', function (Builder $query) {
                $query->unchecked();
            })
            ->get();
    }
}
