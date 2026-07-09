<?php

namespace App\Http\Controllers\Web\Exam;

use Illuminate\Http\Request;
use App\Http\Resources\Exam\ExamIndexResource;
use App\Models\Exam;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class MyExamController
{
    public function index(Request $request): \Inertia\Response
    {
        $now = Carbon::parse($request->input('date')) ?? Carbon::now();
        $start = $now->copy()->startOfDay();
        $end = $now->copy()->endOfDay();

        $exams = Exam::query()
            ->with(['type'])
            ->examiner($request->user())
            ->withCount(['enrollments'])
            ->whereBetween('begin_time',[
                $start,
                $end
            ])
            ->notCancelled()
            ->latest()
            ->get();
        
        return Inertia::render('ExamMonitoring/MyExams', [
            'exams' => ExamIndexResource::collection($exams),
            'current' => $now->copy()->format('d.m.Y'),
            'links' => [
                'prev' => route('my-exams.index', ['date' => $now->copy()->subDay()->format('Y-m-d')]),
                'next' => route('my-exams.index', ['date' => $now->copy()->addDay()->format('Y-m-d')]) 
            ]
        ]);
    }
}