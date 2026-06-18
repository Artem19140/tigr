<?php

namespace App\Modules\Exam\Query;

use App\Modules\Enrollment\Rules\EnrollmentPaymentRules;
use App\Modules\Exam\Resolver\ExamResultResolver;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;

class ExamViewBuilder
{
    public function __construct(
        protected EnrollmentPaymentRules $enrollmentPaymentRules,
        protected ExamResultResolver $resolver
    ){}
    public function execute(
        Exam $exam, 
        Employee $employee,
    ): Exam
    {
        $exam->load([
            'examiners',
            'address',
            'type',
        ]);

        if($employee->can('viewAny', Enrollment::class)){
            $exam->load(['enrollments' => [
                    'foreignNational', 
                    'attempt.center'
                ]
            ]);

            $exam->enrollments->each(function(Enrollment $enrollment) use (
                $exam
            ){
                $enrollment->setAttribute('payment_available', 
                    $this->enrollmentPaymentRules->check($enrollment, $exam)->available
                );
                
                $enrollment->setAttribute('exam_result', 
                    $this->resolver->execute(
                        $enrollment,
                        $exam,
                        $enrollment->attempt
                    )
                );
            });

            
        }

        $exam->loadExists([
            'attempts as has_unchecked_attempts' => function ($query) {
                $query->unchecked();
            },
            'attempts as has_active_attempts' => function ($query) {
                $query->active();
            },
            'attempts as has_attempts'
        ]);

        $exam->loadCount('enrollments');
        
        return $exam;
    }
}
