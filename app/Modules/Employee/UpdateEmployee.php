<?php

namespace App\Modules\Employee;

use App\Http\Dto\EmployeeDto;
use App\Models\Employee;
use App\Support\ModelChangesLogger;
use Illuminate\Support\Facades\DB;

class UpdateEmployee
{
    public function __construct(
        protected ModelChangesLogger $logger,
        protected EmployeeBeforeSaveValidator $validator
    ){}
    public function execute(
        EmployeeDto $dto, 
        Employee $actor,
        Employee $employeeToUpdate
    ):void {
        $this->validator->validate(
            $dto,
            $actor
        );

        DB::transaction(function () use ($employeeToUpdate, $dto) {
            $employeeToUpdate->update($this->getAttributes($dto));
            $rolesChanges = $employeeToUpdate->roles()->sync($dto->rolesIds);
            $this->logger->log($employeeToUpdate,[
                'roles' => $rolesChanges
            ]);
        });
    }

    protected function getAttributes(EmployeeDto $dto): array
    {
        return [
            'email' => $dto->email,
            'job_title' => $dto->jobTitle,
            'name' => $dto->name,
            'surname' => $dto->surname,
            'patronymic' => $dto->patronymic,
        ];
    }
}
