<?php

namespace App\Modules\Enrollment\Action;

use App\Modules\Enrollment\Rules\EnrollmentPaymentRules;
use App\Exceptions\BusinessException;
use App\Models\Enrollment;
use App\Support\ModelChangesLogger;

class ChangePaymentStatusAction
{
    public function __construct(
        protected EnrollmentPaymentRules $enrollmentPaymentRules,
        protected ModelChangesLogger $logger
    ) {}

    public function execute(Enrollment $enrollment): void
    {
        $result = $this->enrollmentPaymentRules->check(
            $enrollment, 
            $enrollment->exam
        );

        if($result->isNotAvailable()){
            throw new BusinessException($result->reason());
        }

        $enrollment->has_payment = ! $enrollment->has_payment;

        $enrollment->save();

        $this->logger->log($enrollment);
    }
}
