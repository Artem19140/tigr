<?php

namespace App\Modules\Enrollment;

use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;

class EnrollmentActionsBuilder
{
    public function __construct(
        protected EnrollmentPaymentRules $paymentRules,
        protected EnrollmentCancelledRules $cancelledRules
    ){}
    
    public function build(
        Enrollment $enrollment,
        Exam $exam,
        Employee $employee
    ): array {
        return collect([
            'payment' => [
                'can' => $employee->can('paymentAny', Enrollment::class),
                'disabled' => ! $this->paymentRules->check($enrollment)->available,
                'url' => route('enrollments.payment-change', [
                    'enrollment' => $enrollment,
                    'status' => ! $enrollment->hasPayment()
                ], false)
            ],

            'statement' => [
                'can' => $employee->can('statementAny', Enrollment::class),
                'url' => route('enrollments.statements', [
                    'enrollment' => $enrollment
                ], false)
            ],

            'approval' => [
                'can' => $employee->can('statementAny', Enrollment::class),
                'url' => route('enrollments.approvals', [
                    'enrollment' => $enrollment
                ], false)
            ],

            'changeRegNumber' => [
                'can' => $employee->can('update', Enrollment::class),
                'url' =>  route('enrollments.update.reg-number',[
                    'enrollment' => $enrollment
                ])
            ],

            'delete' => [
                'can' => $employee->can('delete', Enrollment::class),
                'disabled' => ! $this->cancelledRules->check(
                    $enrollment,
                    $exam
                )->available,
                'url' =>   route('enrollments.destroy',[
                    'enrollment' => $enrollment,
                    'exam' => $exam
                ])
            ]

        ])
        ->filter(function($action){
            return $action['can'];
        })
        ->map(function(array $item){
            unset($item['can']);
            return $item;
        })
        ->toArray();
    }

    
}