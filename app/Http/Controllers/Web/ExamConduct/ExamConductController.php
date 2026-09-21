<?php

namespace App\Http\Controllers\Web\ExamConduct;

use App\Http\Resources\Exam\ExamConductResource;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Modules\Exam\ProtocolCommentRules;
use App\Modules\Exam\UpdateProtocolComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExamConductController
{
    public function show(
        Exam $exam
    ): \Inertia\Response {
        
        $exam->load([
            'enrollments' => [
                'foreignNational',
                'attempt'
            ],
            'type'
        ]);

        $exam->loadState(); //Для поллинга
        
        $exam->enrollments->each(function(Enrollment $enrollment) use (
            $exam
        ){
            $enrollment->setRelation('exam', $exam);
            $enrollment->attempt?->setRelation('exam', $exam);
        });

        return Inertia::render('ExamConduct/View', [
            'exam' => new ExamConductResource($exam),
            'commentUrl' => route('exams.protocol-comments.edit', [
                'exam' => $exam
            ], false),
            'polling' => $exam->needPolling(),
            'comment' => [
                'url' => route('exams.protocol-comments.edit', [
                    'exam' => $exam
                ], false),
                'availability' => [
                    'disabled' => app(ProtocolCommentRules::class)->check($exam)->isNotAvailable()
                ]
            ]
        ]); 
    }

    public function protocolCommentEdit(Exam $exam)
    {
        return Inertia::render('ExamConduct/ProtocolComment', [
            'exam' =>[
                'id' => $exam->id,
                'comment' => $exam->protocol_comment,
                'shortName' => $exam->type->short_name,
                'date' => $exam->begin_time_local->format('H:i d.m.Y')
            ],
            
            'updateUrl' => route('exams.protocol-comments.update', [
                'exam' => $exam
            ], false),

            'backUrl' => route('exams.conduct', [
                'exam' => $exam
            ], false)
        ]);
    }

    public function protocolCommentUpdate(
        Request $request,
        Exam $exam,
        UpdateProtocolComment $updateProtocolComment
    ): RedirectResponse {

        $request->validate([
            'protocolComment' => ['required', 'string'],
        ]);

        $updateProtocolComment->execute(
            $exam,
            $request->input('protocolComment')
        );

        Inertia::flash([
            'success' => 'Комментарий обновлен'
        ]);

        return redirect()->route('exams.conduct', [
            'exam' => $exam
        ]);
    }
}
