<?php

namespace App\Http\Controllers\Web\Exam;

use App\Modules\Shared\CenterData;
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
        $now->setTimezone(CenterData::timeZome());
        $start = $now->copy()->startOfDay()->utc();
        $end = $now->copy()->endOfDay()->utc();

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
        
        $route = 'my-exams.index';
        return Inertia::render('Exam/MyExams', [
            'exams' => ExamIndexResource::collection($exams),
            'current' => $now->copy()->format('d.m.Y'),
            'links' => [
                'prev' => route($route, [
                    'date' => $now->copy()->subDay()->format('Y-m-d')
                ]),
                'next' => route($route, [
                    'date' => $now->copy()->addDay()->format('Y-m-d')
                ]) 
            ]
        ]);
    }
}