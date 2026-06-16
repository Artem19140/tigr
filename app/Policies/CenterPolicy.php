<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Center;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class CenterPolicy
{
    public function viewAny(Employee $employee): bool
    {
        return false;
    }

    public function view(Employee $employee, Center $center): bool
    {
        if(! $this->access($employee, $center)){
            return false;
        }

        if($employee->hasAnyRole(EmployeeRole::CenterAdmin)){
            return true;
        }

        return false;
    }

    public function create(Employee $employee): bool
    {
        return false;
    }

    public function update(Employee $employee, Center $center): bool
    {
        if(! $this->access($employee, $center)){
            return false;
        }

        if($employee->hasAnyRole(EmployeeRole::CenterAdmin)){
            return true;
        }

        return false;
    }

    public function access(Employee $employee, Center $center): bool
    {
        return $employee->center_id === $center->id;
    }
    
    public function delete(Employee $employee, Center $center): bool
    {
        return false;
    }
}
