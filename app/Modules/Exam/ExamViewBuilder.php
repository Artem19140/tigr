<?php

namespace App\Modules\Exam;

use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;

class ExamViewBuilder
{
    public function execute(
        Exam $exam, 
        Employee $employee,
    ): Exam
    {
        $exam->load([
            'examiners',
            'address',
            'type'
        ]);

        if($employee->can('viewAny', Enrollment::class)){
            $exam->load(['enrollments' => [
                    'foreignNational', 
                    'attempt'
                ]
            ]);

            $exam->enrollments = $exam->enrollments
                ->sortBy('foreignNational.surname_normalized');

            $exam->enrollments->each(function(Enrollment $enrollment) use (
                $exam
            ){
                $enrollment->setRelation('exam', $exam);
            });
        }
        
        return $exam;
    }
}
