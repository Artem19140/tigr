<?php

namespace App\Domain\Exam\Query;

use App\Models\Exam;

class ExamShowQuery
{
    public function execute(Exam $exam): Exam
    {
        $exam->load([
            'examiners',
            'address',
            'type',
            'enrollments' => ['foreignNational', 'attempt.center'],
        ]);

        $exam->loadExists([
            'attempts as has_unchecked_attempts' => function ($query) {
                $query->unchecked();
            },
            'attempts as has_active_attempts' => function ($query) {
                $query->active();
            },
            'attempts as has_attempts'
        ]);

        $exam->enrollments->each(function ($enrollment) use ($exam) {
            $enrollment->setRelation('exam', $exam);
        });

        $exam->loadCount('enrollments');
        $exam->enrollments->loadExists('attempt');
        
        return $exam;
    }
}
