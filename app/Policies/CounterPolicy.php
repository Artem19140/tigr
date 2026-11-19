<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class CounterPolicy
{
    public function viewAny(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::CountersViewAny
        );
    }
    public function update(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::CountersUpdate
        );
    }

    
}
