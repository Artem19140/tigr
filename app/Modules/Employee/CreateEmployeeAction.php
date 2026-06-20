<?php

namespace App\Modules\Employee;

use App\Http\Dto\EmployeeDto;
use App\Models\Center;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CreateEmployeeAction
{
    public function __construct(
        protected EmployeeBeforeSaveValidator $validator
    ){}
    public function execute(
        EmployeeDto $dto, 
        Center $center,
        Employee $creator
    ):void {
        $this->validator->validate(
            $dto,
            $creator
        );

        DB::transaction(function () use ($dto, $center) {
            $employee = Employee::create($this->getAttributes($dto, $center));
            $rolesChanges = $employee->roles()->sync($dto->rolesIds);
            $this->log($employee, [
                'roles' => $rolesChanges
            ]);
        });
        
    }

    protected function getAttributes(EmployeeDto $dto, $center): array
    {
        return [
            'email' => $dto->email,
            'name' => $dto->name,
            'surname' => $dto->surname,
            'patronymic' => $dto->patronymic,
            'job_title' => $dto->jobTitle,
            'password' => Hash::make($dto->password),
            'center_id' =>  $center->id
        ];
    }

    protected function log(Employee $employee, array $rolesIds): void
    {
        Log::info('employee_created', [
            'employee_id' => $employee->id,
            'rolesIds' => $rolesIds
        ]);
    }
}
