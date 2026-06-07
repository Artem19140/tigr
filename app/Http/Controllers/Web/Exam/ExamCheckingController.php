<?php

namespace App\Http\Controllers\Web\Exam;

use App\Domain\Exam\Query\GetExamsToCheckQuery;
use App\Http\Resources\Exam\ExamCheckingResource;
use App\Http\Resources\Exam\ExamIndexResource;
use App\Models\Exam;
use App\Support\CenterIsolationCheck;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ExamCheckingController
{
    public function index(
        Request $request,
        GetExamsToCheckQuery $getExamsToCheckQuery
    ): Response {
        $exams = $getExamsToCheckQuery->execute($request->user());
        CenterIsolationCheck::check($exams);
        return Inertia::render('ExamsChecking/ExamsChecking', [
            'exams' => ExamIndexResource::collection($exams),
        ]);
    }

    public function show(Exam $exam): Response
    {
        Gate::authorize('examiner', $exam);

        if (! $exam->type->need_human_check) {
            abort(403);
        }

        $exam->load([
            'type',
            'enrollments' => function (HasMany $query) {
                $query->whereHas('attempt')
                    ->with('attempt.center');
            },
        ]);

        return Inertia::render('ExamsChecking/ExamChecking', [
            'exam' => new ExamCheckingResource($exam),
        ]);
    }
}
