<?php

namespace App\Domain\Enrollment\Action;

use App\Domain\Enrollment\Rules\EnrollmentPaymentRules;
use App\Domain\Exam\Guard\ExamGuard;
use App\Exceptions\BusinessException;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Log;

class ChangePaymentStatusAction
{
    public function __construct(
        protected ExamGuard $examGuard,
        protected EnrollmentPaymentRules $enrollmentPaymentRules
    ) {}

    public function execute(Enrollment $enrollment): void
    {

        $enrollment->load('exam');
        $enrollment->loadExists('attempt');

        $result = $this->enrollmentPaymentRules->check($enrollment);

        if(! $result->available){
            throw new BusinessException($result->reason);
        }

        $enrollment->has_payment = ! $enrollment->has_payment;

        $enrollment->save();

        Log::info('enrollment_payment_change', [
            'payment_status' => $enrollment->has_payment,
            'enrollment_id' => $enrollment->id,
        ]);
    }
}
