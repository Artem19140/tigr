<?php

namespace App\Modules\ExamDocument;

use App\Models\Employee;
use App\Models\Exam;

class ExamDocumentBuilder
{
    public function __construct(
        private ExamDocumentRules $rules
    ){}
    
    public function build(
        Exam $exam,
        Employee $employee
    ): array 
    {
        $docs = [];

        $exam->loadExists([
            'enrollments',
            'attempts',
            'enrollments as enrollments_with_no_attempts_exists' => function($query){
                return $query->whereDoesntHave('attempt');
            },
            'attempts as active_attempts_exists' => function ($query) {
                $query->active();
            },
            'attempts as unchecked_attempts_exists' => function ($query) {
                return $query->unchecked();
            }
        ]);

        if($employee->can('codes', $exam)){
            $result =  $this->rules->codes($exam);
            $docs['codes'] = [
                'url' => route('exams.documents.codes.availability', [
                    'exam' => $exam
                ], false),
                'availability' => [
                    'disabled' => $result->isNotAvailable(),
                    'code' => $result->code()
                ]
            ];
        }

        if($employee->can('protocol', $exam)){
            $result =  $this->rules->protocol($exam);
            $docs['protocol'] = [
                'url' => route('exams.documents.protocol.availability', [
                    'exam' => $exam
                ], false),
                'availability' => [
                    'disabled' => $result->isNotAvailable(),
                    'code' => $result->code()
                ]
            ];
        }

        if($employee->can('results', $exam)){
            $result =  $this->rules->results($exam);
            $docs['results'] = [
                'url' => route('exams.documents.results.availability', [
                    'exam' => $exam
                ], false),
                'availability' => [
                    'disabled' => $result->isNotAvailable(),
                    'code' => $result->code()
                ]
            ];
        }

        if($employee->can('list', $exam)){
            $result =  $this->rules->list($exam);
            $docs['list'] = [
                'url' => route('exams.documents.list.availability', [
                    'exam' => $exam
                ], false),
                'availability' => [
                    'disabled' => $result->isNotAvailable(),
                    'code' => $result->code()
                ]
            ];
        }

        return $docs;
    }    
}