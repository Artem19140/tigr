<?php

namespace App\Navigation;

use App\Models\Employee;
use App\Models\Exam;

class ExamNavigation
{
    public function resolve(Employee $employee, Exam $exam)
    {
        return collect([
            'view' => [
                'url' => route('exams.show', ['exam' => $exam], false),
                'can' =>   $employee->can('view', $exam)
            ],

            'conduct' => [
                'url' => route('exams.conduct', ['exam' => $exam], false),
                'can' => $employee->can('conduct', $exam),
            ],
            
            'review' => [
                'url' => route('exams.review', ['exam' => $exam], false),
                'can' => $employee->can('review', $exam) && $exam->type->need_human_check
            ]
        ])->filter(function($item){
            return $item['can'];
        });
    }
}