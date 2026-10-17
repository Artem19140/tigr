<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Models\AttemptAnswer;
use App\Modules\ExamSession\FinishAttempt;
use App\Modules\ExamSession\ExamSessionAttemptBuilder;
use App\Http\Resources\Attempt\AttemptExamSessionResource;
use App\Models\Attempt;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AttemptTakingController
{
    public function show(
        Attempt $attempt,
        ExamSessionAttemptBuilder $builder
    ): \Inertia\Response {
        $attempt = $builder->build($attempt);

        return Inertia::render('ExamSession/Test', [
            'attempt' => new AttemptExamSessionResource($attempt),
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

        $attemptAnswer->update([
            'audio_played_at' => Carbon::now()
        ]);

        return response()->noContent();
    }
}
