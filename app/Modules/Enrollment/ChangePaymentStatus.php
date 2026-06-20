<?php

namespace App\Modules\Enrollment;

use App\Modules\Enrollment\EnrollmentPaymentRules;
use App\Exceptions\BusinessException;
use App\Models\Enrollment;
use App\Support\ModelChangesLogger;

class ChangePaymentStatus
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
            throw new BusinessException($result->message());
        }

        $enrollment->has_payment = ! $enrollment->has_payment;

        $enrollment->save();

        $this->logger->log($enrollment);
    }
}
