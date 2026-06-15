<?php

namespace App\Modules\Employee;

use App\Enums\EmployeeRole;
use App\Http\Resources\Employee\EmployeeResource;
use App\Models\Employee;
use App\Models\Role;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UpdateEmployeeAction
{
    public function execute(array $data, Employee $employeeToUpdate)
    {
        $this->ensureHasNoRolePlatformAdmin($data);
        $this->ensureCenterAdminValidCreation($data);
        $before = new EmployeeResource($employeeToUpdate)->resolve();
        DB::transaction(function () use ($employeeToUpdate, $data) {
            $employeeToUpdate->update($this->getAttributes($data));
            $employeeToUpdate->roles()->sync($data['roles']);
        });

        $this->log($employeeToUpdate, $before);
    }

    protected function ensureUniqueEmail(string $email, int $id)
    {
        $emailNotUnique = Employee::where('email', $email)
            ->where('id', '<>', $id)
            ->exists();
        if ($emailNotUnique) {
            throw ValidationException::withMessages([
                'email' => 'Такой email уже занят',
            ]);
        }
    }

    protected function ensureHasNoRolePlatformAdmin(array $data): void
    {
        $platformAdminRole = Role::findByEnum(EmployeeRole::PlatformAdmin);
        $hasRolePlatformAdmin = \in_array($platformAdminRole->id, $data['roles']);
        if ($hasRolePlatformAdmin) {
            abort(404);
        }
    }

    protected function ensureCenterAdminValidCreation(array $data): void
    {
        $centerAdminRole = Role::findByEnum(EmployeeRole::CenterAdmin);

        $centerAdminCreating = \in_array($centerAdminRole->id, $data['roles']);
        $creatorIsNotPlatformAdmin = ! auth()->user()->isPlatformAdmin();

        if($centerAdminCreating && $creatorIsNotPlatformAdmin){
            abort(404);
        }
    }

    protected function getAttributes(array $data): array
    {
        return [
            'email' => $data['email'],
            'job_title' => $data['jobTitle'],
            'name' => $data['name'],
            'surname' => $data['surname'],
            'patronymic' => $data['patronymic'],
        ];
    }

    protected function log(Employee $updatedEmployee, array $before): void
    {
        Log::info('employee_updated', [
            'employee_updated_id' => $updatedEmployee->id,
            'changes' => [
                'before' => $before,
                'after' => new EmployeeResource($updatedEmployee)->resolve(),
            ],
        ]);
    }
}
