<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Address;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class AddressPolicy
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
    public function create(Employee $employee): bool
    {
        if($employee->hasAnyRole(
            EmployeeRole::Director,
            EmployeeRole::CenterAdmin
        )){
            return true;
        }
        return false;
    }

    public function update(Employee $employee, Address $address): bool
    {
        if($employee->hasAnyRole(
            EmployeeRole::Director,
            EmployeeRole::CenterAdmin
        )){
            return true;
        }
        return false;
    }

    public function delete(Employee $employee, Address $address): bool
    {
        if($employee->hasAnyRole(
            EmployeeRole::Director,
            EmployeeRole::CenterAdmin
        )){
            return true;
        }
        return false;
    }

    public function restore(Employee $employee, Address $address): bool
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
