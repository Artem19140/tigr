<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Modules\AttemptAnswer\HandleAttemptAnswer;
use App\Http\Requests\AttemptAnswer\AttemptAnswerRequest;
use App\Http\Resources\AttemptAnswer\AttemptAnswerResource;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class AnswerController
{
    public function update(
        AttemptAnswerRequest $request,
        Attempt $attempt,
        AttemptAnswer $attemptAnswer,
        HandleAttemptAnswer $handleAttemptAnswer
    ): JsonResource {
        $foreignNationalAnswer = $request->input('answer');

        $updatedAnswer = DB::transaction(function () use (
            $foreignNationalAnswer, 
            $attempt, 
            $attemptAnswer, 
            $handleAttemptAnswer
        ) {
            $answer = $handleAttemptAnswer->execute($foreignNationalAnswer, $attemptAnswer);
            $attempt->update([
                'last_activity_at' => Carbon::now()
            ]);

            return $answer;
        });

        return new AttemptAnswerResource($updatedAnswer);
    }
}
