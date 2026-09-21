<?php

namespace App\Http\Controllers\Web\ExamReview;

use App\Http\Resources\AttemptAnswer\AttemptAnswerResource;
use App\Models\AttemptAnswer;
use App\Modules\Attempt\FinishManualReview;
use App\Enums\TaskType;
use App\Http\Resources\Attempt\AttemptReviewResource;
use App\Models\Attempt;
use App\Modules\AttemptAnswer\RateAttemptAnswer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttemptReviewController
{
    public function show(Attempt $attempt)
    {
        $attempt->load([
            'taskVariants' => function (BelongsToMany $query) use($attempt){
                $query->whereHas('task', function (Builder $q){
                    $q->whereIn('type', TaskType::manualReviewTypes());
                })
                ->with([
                    'answers',
                    'task',
                    'attemptAnswers' => function ($query) use ($attempt) {
                        $query->where('attempt_id', $attempt->id);
                    }
                ]);
            },
        ]);

        $attempt->taskVariants = $attempt
            ->taskVariants
            ->sortBy('task.order');

        return Inertia::render('ExamReview/AttemptReview', [
            'attempt' => new AttemptReviewResource($attempt),
            'finishUrl' => route('attempts.review.finish', [
                'attempt' => $attempt
            ],  false),
            'backUrl' => route('exams.review', [
                'exam' => $attempt->exam_id
            ], false)
        ]);
    }
    

    public function finish(
        Attempt $attempt,
        FinishManualReview $finishManualReview
    ): RedirectResponse {

        $attempt = $finishManualReview
            ->execute($attempt);

        return redirect()->route('exams.review', [
            'exam' => $attempt->exam
        ]);
    }

    public function rateAnswer(
        Request $request,
        Attempt $attempt,
        AttemptAnswer $attemptAnswer,
        RateAttemptAnswer $rateAttemptAnswer
    ) {

        $request->validate([
            'mark' => ['required', 'integer', 'min:0'],
        ]);

        $attemptAnswer = $rateAttemptAnswer->execute(
            $attemptAnswer, 
            $request->input('mark')
        );

        return response()->json([
            'attemptAnswer' => new AttemptAnswerResource($attemptAnswer),
        ]);
    }
    
}
