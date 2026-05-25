<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Domain\Attempt\Action\BanAttemptAction;
use App\Domain\Attempt\Action\FinishAttemptAction;
use App\Domain\Attempt\Action\StartAttemptAction;
use App\Domain\Attempt\Query\GetCurrentAttemptQuery;
use App\Exceptions\BusinessException;
use App\Http\Resources\Attempt\AttemptExamResource;
use App\Http\Resources\Exam\ExamShortResource;
use App\Models\Attempt;
use App\Models\Exam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AttemptController
{
    public function show(
        Attempt $attempt,
        GetCurrentAttemptQuery $getCurrentAttemptQuery
    ): \Inertia\Response {
        if (! $attempt->isStarted()) {
            $exam = Exam::with([
                'type',
            ])->find($attempt->exam_id);

            return Inertia::render('Attempt/PrepareAttempt', [
                'exam' => new ExamShortResource($exam),
                'duration' => $exam->type->duration,
                'minMark' => $exam->type->min_mark,
                'attempt' => $attempt,
                'tasksCount' => $exam->type->tasks_count,
            ]);
        }

        $attempt = $getCurrentAttemptQuery->execute($attempt);

        return Inertia::render('Attempt/Attempt', [
            'attempt' => new AttemptExamResource($attempt),
        ]);
    }

    public function start(
        StartAttemptAction $startAttempt,
        Attempt $attempt
    ): RedirectResponse {
        if ($attempt->isStarted()) {
            Log::warning('trying to start not pending attempt');
            throw new BusinessException('Начать возможно только неначатую попытку');
        }

        $startedAttempt = $startAttempt->execute($attempt);

        return redirect()->route('attempts.show', ['attempt' => $startedAttempt->id]);
    }

    public function ban(
        Request $request,
        Attempt $attempt,
        BanAttemptAction $banAttempt
    ): Response {
        Gate::authorize('examiner', $attempt->exam);

        $request->validate([
            'banReason' => ['required', 'string'],
        ]);
        $banAttempt->execute($attempt, $request->input('banReason'), $request->user());

        return response()->noContent();
    }

    public function finish(
        Attempt $attempt,
        FinishAttemptAction $finishAttempt,
        Request $request
    ): RedirectResponse {
        $finishAttempt->execute($attempt);
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('attempts.finish');
    }

    public function preparing(Attempt $attempt): \Inertia\Response
    {
        if ($attempt->isStarted()) {
            abort(404);
        }

        $exam = Exam::with([
            'type',
        ])->find($attempt->exam_id);

        return Inertia::render('Attempt/PrepareAttempt', [
            'exam' => new ExamShortResource($exam),
            'duration' => $exam->type->duration,
            'minMark' => $exam->type->min_mark,
            'attempt' => $attempt,
            'tasksCount' => $exam->type->tasks_count,
        ]);
    }
}
