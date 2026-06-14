<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use App\Models\ForeignNational;

class ForeignNationalPolicy
{
    use BasePolicy;

    public function view(Employee $employee, ForeignNational $foreignNational): bool
    {
        if ($this->notSameCenter($employee, $foreignNational)) 
        {
            return false;
        }

        if ($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Director
        )) 
        {
            return true;
        }

        if ($employee->hasRole(EmployeeRole::Examiner->value)) 
        {
            return $foreignNational->exams()
                ->examiner($employee)
                ->exists();
        }

        return false;
    }

    public function viewAny(Employee $employee): bool
    {
        if ($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Director
        )) {
            return true;
        }

        return false;
    }

    public function create(Employee $employee): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Operator);
    }

    public function update(Employee $employee, ForeignNational $foreignNational): bool
    {
        if ($this->notSameCenter($employee, $foreignNational)) {
            return false;
        }

        return $employee->hasAnyRole(EmployeeRole::Operator);
    }

    public function export(Employee $employee): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Director);
    }
}
