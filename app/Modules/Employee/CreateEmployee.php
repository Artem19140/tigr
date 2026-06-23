<?php

namespace App\Modules\Employee;

use App\Http\Dto\EmployeeDto;
use App\Models\Center;
use App\Models\Employee;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class CreateEmployee
{
    public function __construct(
        protected EmployeeBeforeSaveValidator $validator,
        protected Audit $audit
    ){}
    public function execute(
        EmployeeDto $dto, 
        Center $center,
        Employee $creator
    ): Employee {
        $this->validator->validate(
            $dto,
            $creator
        );

        return DB::transaction(function () use ($dto, $center) {
            $employee = Employee::create([
                ...$dto->toArray(),
                'center_id' =>  $center->id
            ]);

            $roles = $employee->roles()->sync($dto->rolesIds);

            $this->audit->log(
                'create',
                $employee,
                ['roles' => $roles]
            );
            Password::sendResetLink([
                'email' => $employee->email
            ]);
            return $employee;
        });
        
    }
}
