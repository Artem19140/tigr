<?php

namespace App\Modules\Employee;

use App\Http\Dto\EmployeeDto;
use App\Models\Center;
use App\Models\Employee;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
    ):void {
        $this->validator->validate(
            $dto,
            $creator
        );

        DB::transaction(function () use ($dto, $center) {
            $employee = Employee::create([
                ...$dto->toArray(),
                'center_id' =>  $center->id,
                'password' => Hash::make($dto->password),
            ]);

            $roles = $employee->roles()->sync($dto->rolesIds);

            $this->audit->log(
                'create',
                $employee,
                ['roles' => $roles]
            );
        });
        
    }
}
