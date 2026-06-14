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
            throw new BusinessException('Нарушение возможно добавить только в день сдачи попытки');
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
        abort_if($attempt->id !== $violation->attempt_id, 404);

        if(! $attempt->canEditViolation()){
            throw new BusinessException('Нарушение возможно редактировать только в день сдачи попытки');
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
        abort_if($attempt->id !== $violation->attempt_id, 404);

        if(! $attempt->canEditViolation()){
            throw new BusinessException('Нарушение возможно удалить только в день сдачи попытки');
        }

        $attempt->violations()->where('id', $violation->id)
            ->delete();

        return response()->noContent();
    }

}
