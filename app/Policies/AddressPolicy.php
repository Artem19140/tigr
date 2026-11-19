<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Address;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class AddressPolicy
{
    public function viewAny(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::AddressesViewAny
        );

    }
    public function create(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::AddressesCreate
        );
    }

    public function update(Employee $employee, Address $address): bool
    {
        return $employee->hasPermission(
            Permission::AddressesUpdate
        );
    }

    public function delete(Employee $employee, Address $address): bool
    {
        return $employee->hasPermission(
            Permission::AddressesDelete
        );
    }

    public function restore(Employee $employee, Address $address): bool
    {
        return $employee->hasPermission(
            Permission::AddressesRestore
        );
    }
}
