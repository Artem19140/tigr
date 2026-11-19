<?php

namespace App\Http\Controllers\Web\ExamReview;

use App\Http\Resources\Exam\ExamReviewResource;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;

#[Middleware('auth:web')]
class ExamReviewController
{
    #[Get('exams/{exam}/review', 'exams.review')]
    #[Authorize('review', 'exam')]
    public function show(
        Exam $exam
    ): \Inertia\Response | RedirectResponse {
         if (! $exam->needHumanReview()) {
            Log::warning('UNEXPECTED: try to manual review exam with only auto review',[
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
                ->orderBy('reg_number')
                ->with('attempt');
            },
        ]);

        return Inertia::render('ExamReview/View', [
            'exam' => new ExamReviewResource($exam)
        ]);
    }
}
