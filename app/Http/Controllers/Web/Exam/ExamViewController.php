<?php

namespace App\Http\Controllers\Web\Exam;

use App\Http\Resources\Exam\ExamCheckingResource;
use App\Http\Resources\Exam\ExamConductResource;
use App\Http\Resources\Exam\ExamResource;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Modules\Exam\ExamCancellRules;
use App\Modules\Exam\ExamEditRules;
use App\Modules\Exam\ExamViewBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExamViewController
{
    public function __construct(
        protected ExamViewBuilder $builder
    ){}
    public function show(
        Request $request,
        Exam $exam
    ): \Inertia\Response {

        $employee = $request->user();

        $exam = $this->builder->execute($exam, $employee);

        return Inertia::render('Exam/ExamView',[
            'exam' => new ExamResource($exam),
            ...$this->actions($exam, $employee)
        ]);
        
    }

    public function conduct(
        Exam $exam,
        Request $request
    ): \Inertia\Response {
        
        $exam->load([
            'enrollments' => [
                'foreignNational',
                'attempt.center'
            ],
            'type'
        ]);

        $exam->loadState();
        
        $exam->enrollments->each(function(Enrollment $enrollment) use (
            $exam
        ){
            $enrollment->setRelation('exam', $exam);
            $enrollment->attempt?->setRelation('exam', $exam);
        });

        return Inertia::render('Exam/ExamConduct', [
            'tab' => 'conduct',
            'exam' => new ExamConductResource($exam),
            ...$this->actions($exam, $request->user())
        ]); 
    }

    public function check(
        Exam $exam,
        Request $request
    ): \Inertia\Response {

        $exam->load([
            'type',
            'enrollments' => function (HasMany $query) {
                $query->whereHas('attempt', function( $q ){
                    return $q->whereNotNull('finished_at');
                })
                ->with('attempt.center');
            },
        ]);

        return Inertia::render('Exam/ExamCheck', [
            'exam' => new ExamCheckingResource($exam),
            ...$this->actions($exam, $request->user())
        ]);
    }

    public function videos(
        Exam $exam,
        Employee $employee
    ){
        $exam->load('documents');
        
        return Inertia::render('Exam/ExamVideos', [
            'exam' => new ExamResource($exam),
            ...$this->actions($exam, $employee)
        ]);
    }


    protected function actions(
        Exam $exam,
        Employee $employee
    ): array
    {
        $isExaminer =  $employee->can('examiner', $exam);
        return [
            'actions' => [
                'edit' => [
                    'can' => $employee->can('update', $exam),
                    'available' => app(ExamEditRules::class)->check($exam)->available
                ],
                'cancell' => [
                    'can' => $employee->can('delete', $exam),
                    'available' => app(ExamCancellRules::class)->check($exam)->available
                ],
                'conduct' => [
                    'can' =>$isExaminer
                ] ,
                'check' => [
                    'can' => $isExaminer && $exam->type->need_human_check
                ],
                'videos' => [
                    'can' => $employee->can('video', $exam)
                ]
            ]
        ];
    }
}