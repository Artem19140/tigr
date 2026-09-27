<?php

namespace App\Modules\Employee;

use App\Enums\EmployeeRole;
use App\Exceptions\Employee\EmployeeValidationExcepion;
use App\Http\Dto\EmployeeDto;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class EmployeeBeforeSaveValidator
{
    public function validate(
        EmployeeDto $dto, 
        Employee $actor
    ):void
    {
        $this->ensureHasNoRolePlatformAdmin($dto->rolesIds);
        $this->ensureCenterAdminValidCreation($dto->rolesIds, $actor);
    }

    protected function ensureHasNoRolePlatformAdmin(
        array $rolesIds
    ): void{
        $platformAdminRole = Role::findByEnum(EmployeeRole::PlatformAdmin);

        $hasPlatformAdminRole = \in_array($platformAdminRole->id, $rolesIds);
        if ( $hasPlatformAdminRole ) {
            Log::critical(
                'ANUTHORIZED: employee_validation - roles has platform admin role', 
                [
                    'rolesIds' => $rolesIds
                ]
            );
            abort(404);
        }
    }

    protected function ensureCenterAdminValidCreation(
        array $rolesIds, 
        Employee $actor
    ): void {
        $centerAdminRole = Role::findByEnum(EmployeeRole::CenterAdmin);

        $centerAdminCreating = \in_array($centerAdminRole->id, $rolesIds);
        $creatorIsNotPlatformAdmin = ! $actor->isPlatformAdmin();

        if($centerAdminCreating && $creatorIsNotPlatformAdmin){
            throw new EmployeeValidationExcepion(
                'ANUTHORIZED: employee_validation - center admin creating with no role platform admin', 
                [
                    'rolesIds' => $rolesIds
                ]
            );
        }
    }
}