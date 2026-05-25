<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Domain\AttemptAnswer\Action\HandleAttemptAnswerAction;
use App\Domain\AttemptAnswer\Action\RateAttemptAnswerAction;
use App\Http\Requests\AttemptAnswer\AttemptAnswerRequest;
use App\Http\Resources\AttemptAnswer\AttemptAnswerResource;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class AttemptAnswerController
{
    public function update(
        AttemptAnswerRequest $request,
        Attempt $attempt,
        AttemptAnswer $attemptAnswer,
        HandleAttemptAnswerAction $handleAttemptAnswerAction
    ): JsonResource {
        $this->authorize($attempt, $attemptAnswer);
        $answer = $request->input('answer');

        $savedAnswer = DB::transaction(function () use ($answer, $attempt, $attemptAnswer, $handleAttemptAnswerAction) {
            $answer = $handleAttemptAnswerAction->execute($answer, $attemptAnswer);
            $attempt->last_activity_at = Carbon::now();
            $attempt->save();

            return $answer;
        });

        return new AttemptAnswerResource($savedAnswer);
    }

    public function rate(
        Request $request,
        AttemptAnswer $attemptAnswer,
        RateAttemptAnswerAction $rateAttemptAnswerAction
    ): JsonResponse {
        $request->validate([
            'mark' => ['required', 'integer', 'min:0'],
        ]);
        Gate::authorize('examiner', $attemptAnswer->attempt->exam);

        $attemptAnswer = $rateAttemptAnswerAction->execute($attemptAnswer, $request->input('mark'));

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
        abort_if($attempt->id !== $attemptAnswer->attempt_id, 403);
    }
}
