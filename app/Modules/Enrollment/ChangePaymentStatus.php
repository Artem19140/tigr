<?php

namespace App\Modules\Enrollment;

use App\Modules\Enrollment\EnrollmentPaymentRules;
use App\Exceptions\BusinessException;
use App\Models\Enrollment;

class ChangePaymentStatus
{
    public function __construct(
        protected EnrollmentPaymentRules $rules
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
    }

    protected function ensureChangingAvailable(
        Enrollment $enrollment
    ): void{
        $result = $this->rules->check(
            $enrollment
        );

        if($result->isNotAvailable()){
            throw new BusinessException(
                $result->code()
            );
        }
    }
}
