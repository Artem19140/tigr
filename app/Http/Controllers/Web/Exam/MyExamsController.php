<?php

namespace App\Http\Controllers\Web\Exam;

use App\Modules\Shared\CenterData;
use Illuminate\Http\Request;
use App\Http\Resources\Exam\ExamIndexResource;
use App\Models\Exam;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;

#[Middleware('auth:web')]

class MyExamsController
{
    #[Get('my-exams', 'my-exams.index')]
    #[Authorize('conductAny', Exam::class)]
    public function index(Request $request): \Inertia\Response
    {
        $now = Carbon::parse($request->input('date')) ?? Carbon::now();
        $now->setTimezone(CenterData::timeZone());
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

        return Inertia::render('MyExams/Index', [
            'exams' => ExamIndexResource::collection($exams),
            'current' => $now->copy()->format('Y-m-d'),//->format('d.m.Y'),
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