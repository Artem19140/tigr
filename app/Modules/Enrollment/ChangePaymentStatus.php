<?php

namespace App\Modules\Enrollment;

use App\Modules\Enrollment\EnrollmentPaymentRules;
use App\Exceptions\BusinessException;
use App\Models\Enrollment;
use App\Support\Audit;
use App\Support\ModelAuditChangesBuilder;

class ChangePaymentStatus
{
    public function __construct(
        protected EnrollmentPaymentRules $enrollmentPaymentRules,
        protected Audit $audit,
        protected ModelAuditChangesBuilder $builder
    ) {}

    public function execute(
        Enrollment $enrollment,
        bool $status
    ): void
    {
        $this->ensureChangingAvailable($enrollment);

        $enrollment->update([
            'has_payment' =>  $status
        ]);

        $this->audit->log(
            'update',
            $enrollment,
            [
                'changes' => $this->builder->build($enrollment)
            ]
        );
    }

    protected function ensureChangingAvailable(
        Enrollment $enrollment
    ): void{
        $result = $this->enrollmentPaymentRules->check(
            $enrollment
        );

        if($result->isNotAvailable()){
            throw new BusinessException(
                $result->code()
            );
        }
    }
}
