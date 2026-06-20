<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Exceptions\BusinessException;
use App\Http\Resources\Violation\ViolationResource;
use App\Models\Attempt;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AttemptViolationController
{
    public function index(Attempt $attempt): AnonymousResourceCollection
    {
        $attempt->load('violations');
        return ViolationResource::collection($attempt->violations);
    }

    public function store(
        Request $request,
        Attempt $attempt
    ): JsonResource {
        if(! $attempt->canEditViolation()){
            throw new BusinessException('Нарушение возможно редактировать только во время действия попытки');
        }
        
        $request->validate(['comment' => ['required', 'string']]);
        
        $violation = $attempt->violations()->create([
            'comment' => $request->input('comment'),
        ]);

        return new ViolationResource($violation);
    }

    public function update(
        Request $request,
        Attempt $attempt,
        Violation $violation
    ): JsonResource {
        $this->ensureViolationBelongsAttempt($attempt, $violation);

        if(! $attempt->canEditViolation()){
            throw new BusinessException('Нарушение возможно редактировать только во время действия попытки');
        }

        $request->validate(['comment' => ['required', 'string']]);
        $violation->comment = $request->input('comment');
        $violation->save();

        return new ViolationResource($violation);
    }

    public function destroy(
        Attempt $attempt,
        Violation $violation
    ): Response {
        $this->ensureViolationBelongsAttempt($attempt, $violation);
        if(! $attempt->canEditViolation()){
            throw new BusinessException('Нарушение возможно редактировать только во время действия попытки');
        }

        $attempt->violations()->where('id', $violation->id)
            ->delete();

        return response()->noContent();
    }

    protected function ensureViolationBelongsAttempt(
        Attempt $attempt, 
        Violation $violation
    ): void
    {
        if($attempt->id === $violation->attempt_id){
            return ;
        }

        Log::warning('violation doesnot belong attempt', [
            'attempt_id' => $attempt->id,
            'violation_id' => $violation->id
        ]);

        abort(404);
    }
}