<?php

namespace App\Http\Controllers\Web\Exam;

use App\Exceptions\BusinessException;
use App\Http\Resources\Exam\ExamCheckingResource;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ExamCheckingController
{
    public function show(Exam $exam): Response
    {
        if (! $exam->type->need_human_check) {
            Log::warning('UNEXPECTED: try to check exam with no human checking',[
                'exam_id' => $exam->id
            ]);
            throw new BusinessException('Данный экзамен проверяется автоматически');
        }

        $exam->load([
            'type',
            'enrollments' => function (HasMany $query) {
                $query->whereHas('attempt', function( $q ){
                    return $q->whereNotNull('finished_at');
                })
                ->with('attempt.center');
            },
        ]);

        return Inertia::render('ExamChecking/ExamChecking', [
            'exam' => new ExamCheckingResource($exam),
        ]);
    }
}
