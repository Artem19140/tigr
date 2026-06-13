<?php

namespace App\Domain\Exam\Query;

use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;

class ExamShowQuery
{
    public function execute(Exam $exam, Employee $employee): Exam
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

            $exam->enrollments->each(function ($enrollment) use ($exam) {
                $enrollment->setRelation('exam', $exam);
            });

            $exam->enrollments->loadExists('attempt');
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
