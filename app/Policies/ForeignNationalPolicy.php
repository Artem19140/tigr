<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Enums\Permission;
use App\Models\Employee;
use App\Models\ForeignNational;

class ForeignNationalPolicy
{

    public function view(
        Employee $employee, 
        ForeignNational $foreignNational
    ): bool {
        return $employee->hasPermission(
            Permission::ForeignNationalsView
        );
    }

    public function viewAny(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::ForeignNationalsViewAny
        );
    }

    public function create(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::ForeignNationalsCreate
        );
    }

    public function update(
        Employee $employee, 
        ForeignNational $foreignNational
    ): bool
    {
        return $employee->hasPermission(
            Permission::ForeignNationalsUpdate
        );
    }

    public function findMatches(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::ForeignNationalsFindMatches
        );
    }
}
