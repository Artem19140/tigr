<?php

namespace App\Modules\Employee;

use App\Http\Dto\EmployeeDto;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class CreateEmployee
{
    public function __construct(
        protected EmployeeBeforeSaveValidator $validator
    ){}
    
    public function execute(
        EmployeeDto $dto, 
        Employee $creator
    ): Employee {
        $this->validator->validate(
            $dto,
            $creator
        );

        return DB::transaction(function () use ($dto) {
            $employee = Employee::create([
                'surname' => $dto->surname,
                'name' => $dto->name,
                'patronymic' => $dto->patronymic,
                'email' => $dto->email
            ]);

            $roles = $employee->roles()->sync($dto->rolesIds);

            return $employee;
        });
        
    }
}
