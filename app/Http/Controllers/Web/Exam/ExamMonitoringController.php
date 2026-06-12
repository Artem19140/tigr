<?php

namespace App\Http\Controllers\Web\Exam;

use App\Domain\Exam\Action\Monitoring\UpdateProtocolCommentAction;
use App\Http\Resources\Exam\ExamIndexResource;
use App\Http\Resources\Exam\ExamMonitoringResource;
use App\Models\Exam;
use App\Support\CenterIsolationCheck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ExamMonitoringController
{
    public function index(Request $request): \Inertia\Response
    {

        $past = $request->boolean('past');
        $now = Carbon::parse($request->input('date')) ?? Carbon::now();
        $start = $now->copy()->startOfDay();
        $end = $now->copy()->endOfDay();
        $exams = Exam::query()
            ->with(['type', 'center'])
            ->examiner($request->user())
            ->withCount(['enrollments'])
            ->whereBetween('begin_time',[
                $start,
                $end
            ])
            ->notCancelled()
            ->get();

        CenterIsolationCheck::check($exams);
        
        return Inertia::render('ExamMonitoring/ExamMonitoringList', [
            'exams' => ExamIndexResource::collection($exams),
            'past' => $past,
            'current' => $now->copy()->format('d.m.Y'),
            'links' => [
                // 'prev' => [
                //     'route' => route('exams.monitoring.index', ['date' => $now->copy()->subDay()->format('Y-m-d')]) ,
                //     'label' => $now->copy()->subDay()->format('d.m.Y')]
                // ],
                'prev' => route('exams.monitoring.index', ['date' => $now->copy()->subDay()->format('Y-m-d')]),
                'next' => route('exams.monitoring.index', ['date' => $now->copy()->addDay()->format('Y-m-d')]) 
            ]
        ]);
    }

    public function show(Exam $exam): \Inertia\Response
    {
        $this->authorize($exam);

        $exam->load([
            'enrollments' => ['foreignNational', 'attempt.center'],
            'type',
        ]);
        
        $exam->enrollments->loadExists('attempt');
        $exam->enrollments = $exam->enrollments->sortBy('foreignNational.surname');
        $exam->enrollments->each(function($e)use($exam){
            $e->setRelation('exam', $exam);
        });
        return Inertia::render('ExamMonitoring/ExamMonitoring', [
            'exam' => new ExamMonitoringResource($exam),
            'available' => [
                'protocolComment' => $exam->canEditProtocolComment(),
            ],
        ]);
    }

    public function protocolComment(
        Request $request,
        Exam $exam,
        UpdateProtocolCommentAction $updateProtocolComment
    ): Response {
        $this->authorize($exam);

        $request->validate([
            'protocolComment' => ['required', 'string'],
        ]);

        $updateProtocolComment->execute(
            $exam,
            $request->input('protocolComment')
        );

        return response()->noContent();
    }

    protected function authorize(Exam $exam): void
    {
        Gate::authorize('examiner', $exam);
    }
}
