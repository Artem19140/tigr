<?php

namespace App\Http\Controllers\Web\Exam;

use App\Modules\Enrollment\Rules\EnrollmentPaymentRules;
use App\Modules\Exam\Action\Monitoring\UpdateProtocolCommentAction;
use App\Http\Resources\Exam\ExamIndexResource;
use App\Http\Resources\Exam\ExamMonitoringResource;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Support\CenterIsolationCheck;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class ExamMonitoringController
{
    public function index(Request $request): \Inertia\Response
    {
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
            'current' => $now->copy()->format('d.m.Y'),
            'links' => [
                'prev' => route('exams.monitoring.index', ['date' => $now->copy()->subDay()->format('Y-m-d')]),
                'next' => route('exams.monitoring.index', ['date' => $now->copy()->addDay()->format('Y-m-d')]) 
            ]
        ]);
    }

    public function show(
        Exam $exam, 
        EnrollmentPaymentRules $enrollmentPaymentRules
    ): \Inertia\Response {
        $exam->load([
            'enrollments' => ['foreignNational', 'attempt.center'],
            'type',
        ]);
        
        $exam->enrollments->loadExists('attempt');
        $exam->enrollments = $exam->enrollments->sortBy('foreignNational.surname');
        
        $exam->enrollments->each(function(Enrollment $enrollment) use (
            $exam, 
            $enrollmentPaymentRules
        ){
            $enrollment->setAttribute('payment_available', 
                $enrollmentPaymentRules->check($enrollment, $exam)->available
            );
            $enrollment->attempt?->setRelation('exam', $exam);
        });

        return Inertia::render('ExamMonitoring/ExamMonitoring', [
            'exam' => new ExamMonitoringResource($exam)
        ]);
    }

    public function protocolComment(
        Request $request,
        Exam $exam,
        UpdateProtocolCommentAction $updateProtocolComment
    ): Response {

        $request->validate([
            'protocolComment' => ['required', 'string'],
        ]);

        $updateProtocolComment->execute(
            $exam,
            $request->input('protocolComment')
        );

        return response()->noContent();
    }
}
