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

            $employeeToUpdate->update($dto->toArray());
            $rolesChanges = $employeeToUpdate->roles()->sync($dto->rolesIds);

            $this->logger->log($employeeToUpdate,[
                'roles' => $rolesChanges
            ]);
        });
    }
}
