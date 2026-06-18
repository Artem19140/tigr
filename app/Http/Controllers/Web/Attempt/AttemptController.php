<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Modules\Attempt\Action\AnnulledAttemptAction;
use App\Modules\Attempt\Action\FinishAttemptAction;
use App\Modules\Attempt\Action\StartAttemptAction;
use App\Modules\Attempt\Query\GetCurrentAttemptQuery;
use App\Exceptions\BusinessException;
use App\Http\Resources\Attempt\AttemptExamResource;
use App\Http\Resources\Exam\ExamShortResource;
use App\Models\Attempt;
use App\Models\Exam;
use App\Modules\Shared\SystemSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
                'minTimeFromStartToFinish' => SystemSettings::attemptMinTimeFromStartToFinish()
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
            Log::warning('trying to start not pending attempt', [
                'attempt_id' => $attempt->id
            ]);
            throw new BusinessException('Начать возможно только неначатую попытку');
        }

        $startedAttempt = $startAttempt->execute($attempt);

        return redirect()->route('attempts.show', [
            'attempt' => $startedAttempt->id
        ]);
    }

    public function annul(
        Request $request,
        Attempt $attempt,
        AnnulledAttemptAction $annulledAttempt
    ): Response {

        $request->validate([
            'annulledReason' => ['required', 'string'],
        ]);
        $annulledAttempt->execute($attempt, $request->input('annulledReason'), $request->user());

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

        return redirect()->route('attempts.finish.after');
    }
}
