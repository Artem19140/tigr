<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class CounterPolicy
{
    public function viewAny(Employee $employee): bool
    {
        if($employee->hasAnyRole(
            EmployeeRole::Director,
            EmployeeRole::CenterAdmin
        )){
            return true;
        }
        return false;
    }
    public function update(Employee $employee): bool
    {
        if($employee->hasAnyRole(
            EmployeeRole::Director,
            EmployeeRole::CenterAdmin
        )){
            return true;
        }
        return false;
    }

    
}
