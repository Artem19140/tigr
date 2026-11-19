<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class CenterPolicy
{
    public function view(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::CentersView
        );
    }
    public function edit(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::CentersEdit
        );
    }
}
