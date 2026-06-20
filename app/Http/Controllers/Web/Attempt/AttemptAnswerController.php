<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Modules\AttemptAnswer\HandleAttemptAnswer;
use App\Modules\AttemptAnswer\RateAttemptAnswer;
use App\Http\Requests\AttemptAnswer\AttemptAnswerRequest;
use App\Http\Resources\AttemptAnswer\AttemptAnswerResource;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AttemptAnswerController
{
    public function update(
        AttemptAnswerRequest $request,
        Attempt $attempt,
        AttemptAnswer $attemptAnswer,
        HandleAttemptAnswer $handleAttemptAnswer
    ): JsonResource {
       
        $this->authorize($attempt, $attemptAnswer);
        $foreignNationalAnswer = $request->input('answer');

        $updatedAnswer = DB::transaction(function () use (
            $foreignNationalAnswer, 
            $attempt, 
            $attemptAnswer, 
            $handleAttemptAnswer
        ) {
            $answer = $handleAttemptAnswer->execute($foreignNationalAnswer, $attemptAnswer);
            $attempt->last_activity_at = Carbon::now();
            $attempt->save();

            return $answer;
        });

        return new AttemptAnswerResource($updatedAnswer);
    }

    public function rate(
        Request $request,
        AttemptAnswer $attemptAnswer,
        RateAttemptAnswer $rateAttemptAnswer
    ): JsonResponse {
        Gate::authorize('attempts.employee-access', $attemptAnswer->attempt);
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

    public function audioPlayed(
        Attempt $attempt,
        AttemptAnswer $attemptAnswer,
    ): Response {
        $this->authorize($attempt, $attemptAnswer);

        $attemptAnswer->audio_played = true;
        $attemptAnswer->save();

        return response()->noContent();
    }

    protected function authorize(
        Attempt $attempt,
        AttemptAnswer $attemptAnswer
    ): void {
        abort_if($attempt->id !== $attemptAnswer->attempt_id, 404);
    }
}
