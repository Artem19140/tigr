<?php

namespace App\Modules\Exam;

use App\Modules\Enrollment\EnrollmentPaymentRules;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;

class ExamViewBuilder
{
    public function __construct(
        protected EnrollmentPaymentRules $enrollmentPaymentRules
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
            'documents'
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
                $enrollment->setRelation('exam', $exam);
            });

            
        }

        $exam->loadState();
        $exam->loadCount('enrollments');
        
        return $exam;
    }
}
