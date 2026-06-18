<?php

namespace App\Modules\ForeignNational\Action;

use App\Modules\Enrollment\Action\CreateEnrollmentAction;
use App\Models\Employee;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;


class CreateForeignNationalWithEnrollmentAction
{
    public function __construct(
        protected StoreForeignNationalAction $storeForeignNational,
        protected CreateEnrollmentAction $createEnrollment,
    ) {}

    public function execute(
        array $foreignNationalData,
        int $examId,
        Employee $employee
    ): Enrollment {

        $enrollent = DB::transaction(function () use ($foreignNationalData, $employee, $examId) {
            $foreignNational = $this->storeForeignNational->execute($foreignNationalData, $employee);

            return $this->createEnrollment->execute(
                $examId, 
                $foreignNational->id, 
                $employee, 
                $foreignNationalData['hasPayment']
            );
        });

        return $enrollent;

    }
}
