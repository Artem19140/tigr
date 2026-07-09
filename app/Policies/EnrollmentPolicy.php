<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use App\Models\Enrollment;

class EnrollmentPolicy
{
    public function viewAny(Employee $employee): bool
    {
        if ($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Director,
            EmployeeRole::Examiner
        )) {
            return true;
        }

        return false;
    }

    public function view(Employee $employee, Enrollment $enrollment): bool
    {
        return false;
    }

    public function create(Employee $employee): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Operator);
    }

    public function payment(Employee $employee, Enrollment $enrollment): bool
    {
        if ($employee->hasAnyRole(
            EmployeeRole::Operator
        )) {
            return true;
        }
        return $employee->can('examiner', $enrollment->exam);

    }

    public function paymentAny(Employee $employee): bool
    {

        if ($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Examiner
        )) {
            return true;
        }

        return false;

    }

    public function statement(Employee $employee, Enrollment $enrollment): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Operator);
    }

     public function statementAny(Employee $employee): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Operator);
    }
}
