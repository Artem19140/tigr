<?php

namespace App\Domain\Employee;

use App\Enums\EmployeeRole;
use App\Models\Center;
use App\Models\Employee;
use App\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Facades\Log;

class CreateEmployeeAction
{
    public function execute(
        array $data, 
        Center $center,
        Employee $creator
    ):void {
        $this->ensureHasNoRolePlatformAdmin($data);
        $this->ensureCenterAdminValidCreation($data, $creator);

        DB::transaction(function () use ($data, $center) {

            $employee = Employee::create($this->getAttributes($data, $center));
            $employee->roles()->sync($data['roles']);
            $this->log($employee);

        });
    }

    protected function ensureHasNoRolePlatformAdmin(array $data): void
    {
        $platformAdminRole = Role::findByEnum(EmployeeRole::PlatformAdmin);

        $hasPlatformAdminRole = \in_array($platformAdminRole->id, $data['roles']);
        if ( $hasPlatformAdminRole ) {
            abort(404);
        }
    }

    protected function ensureCenterAdminValidCreation(array $data, Employee $creator): void
    {
        $centerAdminRole = Role::findByEnum(EmployeeRole::CenterAdmin);

        $centerAdminCreating = \in_array($centerAdminRole->id, $data['roles']);
        $creatorIsNotPlatformAdmin = ! $creator->isPlatformAdmin();

        if($centerAdminCreating && $creatorIsNotPlatformAdmin){
            abort(404);
        }
    }

    protected function getAttributes(array $data, $center): array
    {
        return [
            'email' => $data['email'],
            'name' => $data['name'],
            'surname' => $data['surname'],
            'patronymic' => $data['patronymic'],
            'job_title' => $data['jobTitle'],
            'password' => Hash::make($data['password']),
            'center_id' =>  $center->id
        ];
    }

    protected function log(Employee $employee): void
    {
        Log::info('employee_created', [
            'employee_created_id' => $employee->id,
        ]);
    }
}
