<?php

namespace App\Http\Controllers\Web\Exam;

use App\Exceptions\BusinessException;
use App\Http\Resources\Exam\ExamCheckingResource;
use App\Http\Resources\Exam\ExamIndexResource;
use App\Models\Exam;
use App\Support\CenterIsolationCheck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ExamCheckingController
{
    public function index(
        Request $request
    ): Response {
        $exams = Exam::query()
            ->with(['type', 'center'])
            ->examiner($request->user())
            ->whereHas('type', function (Builder $query) {
                $query->where('need_human_check', true);
            })
            ->whereHas('attempts', function (Builder $query) {
                $query->unchecked();
            })
            ->get();

        CenterIsolationCheck::check($exams);
        return Inertia::render('ExamsChecking/ExamsChecking', [
            'exams' => ExamIndexResource::collection($exams),
        ]);
    }

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

        return Inertia::render('ExamsChecking/ExamChecking', [
            'exam' => new ExamCheckingResource($exam),
        ]);
    }
}
