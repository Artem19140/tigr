<?php

namespace App\Modules\Exam;

use App\Enums\EmployeeRole;
use App\Models\Address;
use App\Models\Employee;
use App\Models\ExamType;
use Illuminate\Database\Eloquent\Builder;

class ExamCreateData
{
    public function execute():array
    {
        $examiners = Employee::query()
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', EmployeeRole::Examiner);
            })
            ->active()
            ->get();

        $addresses = Address::query()
            ->where('is_active', true)
            ->get();

        $examTypes = ExamType::cached();

        return [
            'addresses' => $addresses,
            'examTypes' => $examTypes,
            'examiners' => $examiners,
        ];
    }
}