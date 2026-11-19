<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Enums\Permission;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\ForeignNational;

class EnrollmentPolicy
{
    public function viewAny(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::EnrollmentsViewAny
        );

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
        return $employee->hasPermission(
            Permission::EnrollmentsCreate
        );
    }

    public function update(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::EnrollmentsUpdate
        );
    }

    public function payment(Employee $employee, Enrollment $enrollment): bool
    {
        return $employee->can('examiner', $enrollment->exam);
    }

    public function paymentAny(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::EnrollmentsChangePayment
        );
    }

    public function statement(
        Employee $employee, 
        Enrollment $enrollment
    ): bool
    {
        return $employee->hasPermission(
            Permission::EnrollmentsChangePayment
        );
    }

     public function statementAny(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::EnrollmentsStatements
        );
    }

    public function delete(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::EnrollmentsDelete
        );
    }
}
