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

    public function execute(
        Enrollment $enrollment,
        bool $status
    ): void
    {
        $result = $this->enrollmentPaymentRules->check(
            $enrollment
        );

        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }

        $enrollment->update([
            'has_payment' =>  $status
        ]);

        $this->logger->log($enrollment);
    }
}
