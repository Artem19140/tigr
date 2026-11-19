<?php

namespace App\Modules\Employee;

use App\Http\Dto\EmployeeDto;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class UpdateEmployee
{
    public function __construct(
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

            $employeeToUpdate->update([
                'surname' => $dto->surname,
                'name' => $dto->name,
                'patronymic' => $dto->patronymic,
                'email' => $dto->email
            ]);

            $rolesChanges = $employeeToUpdate->roles()
                ->sync($dto->rolesIds);

        });
    } 
}
