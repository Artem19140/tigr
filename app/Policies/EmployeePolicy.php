<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Employee;

class EmployeePolicy
{
    public function viewAny(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::EmployeesViewAny
        );
    }

    public function create(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::EmployeesCreate
        );
    }

    public function update(Employee $actor, Employee $employee ): bool
    {
        if($employee->isPlatformAdmin()){
            return false;
        }

        if($actor->id === $employee->id){
            return false;
        }

        return $employee->hasPermission(
            Permission::EmployeesUpdate
        );
    }

    public function delete(Employee $actor, Employee $employee): bool
    {
        if($employee->isPlatformAdmin()){
            return false;
        }

        if($actor->id === $employee->id){
            return false;
        }

        return $employee->hasPermission(
            Permission::EmployeesDelete
        );
    }
}
