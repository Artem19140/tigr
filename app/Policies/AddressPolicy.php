<?php

namespace App\Policies;

use App\Enums\EmployeeRole;
use App\Models\Address;
use App\Models\Employee;

class AddressPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Address $address): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        if($employee->hasAnyRole(EmployeeRole::CenterAdmin)){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Address $address): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, Address $address): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Employee $employee, Address $address): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Employee $employee, Address $address): bool
    {
        return false;
    }
}
