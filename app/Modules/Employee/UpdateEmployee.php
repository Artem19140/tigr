<?php

namespace App\Modules\Employee;

use App\Http\Dto\EmployeeDto;
use App\Models\Employee;
use App\Support\Audit;
use App\Support\ModelAuditChangesBuilder;
use Illuminate\Support\Facades\DB;

class UpdateEmployee
{
    public function __construct(
        protected EmployeeBeforeSaveValidator $validator,
        protected Audit $audit,
        protected ModelAuditChangesBuilder $builder
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

            $employeeToUpdate->update(
                $dto->toArray()
            );

            $rolesChanges = $employeeToUpdate->roles()
                ->sync($dto->rolesIds);

            $this->log($employeeToUpdate,[
                'roles' => $rolesChanges
            ]);
        });
    }

    protected function log(
        Employee $employee,
        array $relations
    ): void {
        $this->audit->log(
            'update',
            $employee,
            $this->builder->build(
                $employee,
                $relations
            )
        );
    }
    
}
