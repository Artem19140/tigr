<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Modules\Attempt\AnnulAttempt;
use App\Modules\Attempt\FinishAttempt;
use App\Modules\Attempt\StartAttempt;
use App\Modules\Attempt\AttemptExamViewBuilder;
use App\Exceptions\BusinessException;
use App\Http\Resources\Attempt\AttemptExamResource;
use App\Models\Attempt;
use App\Models\Exam;
use App\Modules\Shared\ExamSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AttemptController
{
    public function show(
        Attempt $attempt,
        AttemptExamViewBuilder $builder
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
                'fullName' => $attempt->foreignNational->full_name_short
                
            ]);
        }

        $attempt = $builder->build($attempt);

        return Inertia::render('Attempt/Attempt', [
            'attempt' => new AttemptExamResource($attempt)
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
            'attempt' => $startedAttempt->id
        ]);
    }

    public function annul(
        Request $request,
        Attempt $attempt,
        AnnulAttempt $annulAttempt
    ): Response {

        $request->validate([
            'annulledReason' => ['required', 'string'],
        ]);

        $annulAttempt->execute(
            $attempt, 
            $request->input('annulledReason'), 
            $request->user()
        );

        return response()->noContent();
    }

    public function finish(
        Attempt $attempt,
        FinishAttempt $finishAttempt,
        Request $request
    ): RedirectResponse {
        
        $finishAttempt->execute($attempt);
        
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('attempts.finish.after');
    }
}
