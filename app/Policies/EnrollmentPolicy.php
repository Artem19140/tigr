<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\ForeignNational;

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

    public function examSession(
        ForeignNational $foreignNational, 
        Enrollment $enrollment
    ): bool 
    {   
        return $enrollment->foreign_national_id === $foreignNational->id;
    }
    

    public function create(Employee $employee): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Operator);
    }

    public function update(Employee $employee): bool
    {
        return $employee->hasAnyRole(
            EmployeeRole::Operator, 
            EmployeeRole::Director
        );
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

    public function statement(
        Employee $employee, 
        Enrollment $enrollment
    ): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Operator);
    }

     public function statementAny(Employee $employee): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Operator);
    }

    public function delete(Employee $employee): bool
    {
        return $employee->hasAnyRole(EmployeeRole::Operator);
    }
}
