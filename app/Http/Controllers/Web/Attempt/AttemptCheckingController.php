<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Modules\Attempt\FinishManualChecking;
use App\Enums\TaskType;
use App\Http\Resources\Attempt\AttemptCheckingResource;
use App\Models\Attempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class AttemptCheckingController
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

        return Inertia::render('ExamChecking/AttemptChecking', [
            'attempt' => new AttemptCheckingResource($attempt),
            'examId' => $attempt->exam_id
        ]);
    }
    

    public function finish(
        Attempt $attempt,
        FinishManualChecking $finishManualChecking
    ): RedirectResponse {

        $attempt = $finishManualChecking
            ->execute($attempt);

        return redirect()->route('exam.show.checking', [
            'exam' => $attempt->exam
        ]);
    }
}
