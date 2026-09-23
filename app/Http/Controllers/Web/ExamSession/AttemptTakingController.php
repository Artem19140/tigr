<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Models\AttemptAnswer;
use App\Modules\Attempt\FinishAttempt;
use App\Modules\Attempt\StartAttempt;
use App\Modules\Attempt\ExamSessionAttemptBuilder;
use App\Exceptions\BusinessException;
use App\Http\Resources\Attempt\AttemptExamSessionResource;
use App\Models\Attempt;
use App\Models\Exam;
use App\Modules\Shared\ExamSettings;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AttemptTakingController
{
    public function show(
        Attempt $attempt,
        ExamSessionAttemptBuilder $builder
    ): \Inertia\Response {
        if (! $attempt->isStarted()) {
            $exam = Exam::with([
                'type',
            ])->find($attempt->exam_id);

            return Inertia::render('Attempt/PrepareAttempt', [
                'exam' => [
                    'duration' => $exam->type->duration,
                    'minMark' => $exam->type->min_mark,
                    'attemptId' => $attempt->id,
                    'tasksCount' => $exam->type->tasks_count,
                    'minTimeFromStartToFinish' => ExamSettings::attemptMinDurationMinutes(),
                    'name' => $exam->type->name
                ],
                'fullName' => $attempt->foreignNational->full_name_short,
                'startUrl' => route('attempts.start', [
                    'attempt' => $attempt
                ], false)
            ]);
        }

        $attempt = $builder->build($attempt);

        return Inertia::render('Attempt/Attempt', [
            'attempt' => new AttemptExamSessionResource($attempt),
            'finishUrl' => route('attempts.finish', [
                'attempt' => $attempt
            ], false)
        ]);
    }

    public function start(
        StartAttempt $startAttempt,
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
            'attempt' => $startedAttempt->id,
            'finishUrl' => route('attempts.finish', [
                'attempt' => $attempt
            ], false)
        ]);
    }

    public function finish(
        Attempt $attempt,
        FinishAttempt $finishAttempt,
        Request $request
    ): RedirectResponse {
        
        $finishAttempt->execute($attempt);

        Auth::logout();
        
        $request->session()->invalidate();

        $request->session()->regenerateToken();
        
        return redirect()->route('attempts.finish.after');
    }

    public function audioPlayed(
        Attempt $attempt,
        AttemptAnswer $attemptAnswer,
    ): Response {

        $attemptAnswer->audio_played_at = Carbon::now();
        $attemptAnswer->save();

        return response()->noContent();
    }
}
