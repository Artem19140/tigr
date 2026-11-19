<?php

namespace App\Http\Controllers\Web\ExamReview;

use App\Models\AttemptAnswer;
use App\Modules\ExamReview\FinishManualReview;
use App\Enums\TaskType;
use App\Http\Resources\Attempt\AttemptReviewResource;
use App\Models\Attempt;
use App\Modules\ExamReview\RateAttemptAnswer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\ScopeBindings;

#[Middleware('auth:web')]
#[Authorize('attempts.employee-access', 'attempt')]
#[Prefix('attempts')]
class AttemptReviewController
{ 
    #[Get('{attempt}/review', 'attempts.review')]
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
    
    #[Post('{attempt}/review/finish', 'attempts.review.finish')]
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

    #[Put('{attempt}/answers/{attempt_answer}/rate', 'attempts.answers.rate')]
    #[ScopeBindings()]
    public function rateAnswer(
        Request $request,
        Attempt $attempt,
        AttemptAnswer $attemptAnswer,
        RateAttemptAnswer $rateAttemptAnswer
    ): RedirectResponse {
        $request->validate([
            'mark' => ['required', 'integer', 'min:0'],
        ]);

        $attemptAnswer = $rateAttemptAnswer->execute(
            $attemptAnswer, 
            $request->input('mark')
        );
        
        Inertia::flash([
            'status' => 'ok'
        ]);

        return back();
    }
}
