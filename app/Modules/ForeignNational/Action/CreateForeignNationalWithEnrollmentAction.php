<?php

namespace App\Modules\ForeignNational\Action;

use App\Http\Dto\ForeignNationalStoreDto;
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
        ForeignNationalStoreDto $dto,
        int $examId,
        Employee $employee,
        bool $hasPayment
    ): Enrollment {

        $enrollent = DB::transaction(function () use (
            $employee, 
            $examId, 
            $dto,
            $hasPayment
        ) {
            $foreignNational = $this->storeForeignNational
                ->execute( $dto, $employee,);

            return $this->createEnrollment->execute(
                $examId, 
                $foreignNational->id, 
                $employee, 
                $hasPayment
            );
        });

        return $enrollent;

    }
}
