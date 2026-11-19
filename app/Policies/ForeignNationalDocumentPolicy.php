<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Employee;
use App\Models\ForeignNationalDocument;
use Illuminate\Auth\Access\Response;

class ForeignNationalDocumentPolicy
{
    public function viewAny(Employee $employee): bool
    {
        return $employee->hasPermission(
            Permission::ForeignNationalsDocumentsViewAny
        );
    }

    public function view(
        Employee $employee, 
        ForeignNationalDocument $foreignNationalDocument
    ): bool
    {
        return $employee->hasPermission(
            Permission::ForeignNationalsDocumentsView
        );
    }

    public function update(
        Employee $employee, 
        ForeignNationalDocument $foreignNationalDocument
    ): bool
    {
        return $employee->hasPermission(
            Permission::ForeignNationalsDocumentsUpdate
        );
    }
}