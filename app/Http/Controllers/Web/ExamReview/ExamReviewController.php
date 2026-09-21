<?php

namespace App\Http\Controllers\Web\ExamReview;

use App\Http\Resources\Exam\ExamReviewResource;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ExamReviewController
{
    public function show(
        Exam $exam
    ): \Inertia\Response | RedirectResponse {
         if (! $exam->type->need_human_check) {
            Log::warning('UNEXPECTED: try to check exam with no human checking',[
                'exam_id' => $exam->id
            ]);
            Inertia::flash('error', 'Данный экзамен проверяется автоматически');
            return redirect()->route('exams.show', ['exam' => $exam]);
        }
        $exam->load([
            'type',
            'enrollments' => function (HasMany $query) {
                $query->whereHas('attempt', function( $q ){
                    return $q->whereNotNull('finished_at');
                })
                ->with('attempt');
            },
        ]);

        return Inertia::render('ExamReview/View', [
            'exam' => new ExamReviewResource($exam)
        ]);
    }
}
