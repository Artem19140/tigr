<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use App\Models\ForeignNationalDocument;
use Illuminate\Auth\Access\Response;

class ForeignNationalDocumentPolicy
{
    public function viewAny(Employee $employee): bool
    {
        if($employee->hasAnyRole(
            EmployeeRole::Operator
        )){
            return true;
        }
        return false;
    }

    public function view(
        Employee $employee, 
        ForeignNationalDocument $foreignNationalDocument
    ): bool
    {
        if($employee->hasAnyRole(
            EmployeeRole::Operator
        )){
            return true;
        }

        return false;
    }

    public function update(
        Employee $employee, 
        ForeignNationalDocument $foreignNationalDocument
    ): bool
    {
        if($employee->hasAnyRole(
            EmployeeRole::Operator
        )){
            return true;
        }

        return false;
    }

    public function restore(
        Employee $employee, 
        ForeignNationalDocument $foreignNationalDocument
    ): bool
    {
        return false;
    }
}