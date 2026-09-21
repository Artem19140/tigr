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
    public function view(Employee $employee): bool
    {
        if($employee->hasAnyRole(
            EmployeeRole::Director,
            EmployeeRole::CenterAdmin
        )){
            return true;
        }
        return false;
    }
    public function edit(Employee $employee): bool
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
