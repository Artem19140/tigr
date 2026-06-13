<?php

namespace App\Http\Controllers\Web\Exam;

use App\Domain\Center\CenterContext;
use App\Http\Resources\Exam\ExamCalendarResource;
use App\Models\Exam;
use App\Support\CenterIsolationCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class ExamScheduleController
{
    public function __construct(
        protected CenterContext $centerContext
    ){}
    public function index(Request $request): \Inertia\Response
    {
        $request->validate([
            'dateFrom' => ['sometimes', 'date'],
            'dateTo' => ['sometimes', 'date'],
        ]);

        $dateFrom = $request->input('dateFrom') ?
            Carbon::parse($request->input('dateFrom'))
            : Carbon::now();

        $dateTo = $request->input('dateTo') ? 
            Carbon::parse($request->input('dateTo')) : 
            Carbon::now();

        $exams = Exam::query()
            ->forCenter($this->centerContext->id())
            ->visibleFor($request->user())
            ->with(['type', 'center'])
            ->whereBetween('begin_time', [
                $dateFrom->copy()->startOfMonth(),
                $dateTo->copy()->endOfMonth()
            ])
            ->get();

        CenterIsolationCheck::check($exams);
        
        return Inertia::render('Schedule/Schedule', [
            'exams' => ExamCalendarResource::collection($exams),
            'permissions' => [
                'create' => $request->user()->can('create', Exam::class),
            ],
            'links' => [
                'prev' => route('exams.schedule.index', [
                    'dateFrom' => $dateFrom->copy()->subMonth()->startOfMonth()->format('Y-m-d'),
                    'dateTo' => $dateFrom->copy()->subMonth()->endOfMonth()->format('Y-m-d')
                ]),
                'next' => route('exams.schedule.index', [
                    'dateFrom' => $dateFrom->copy()->addMonth()->startOfMonth()->format('Y-m-d'),
                    'dateTo' => $dateFrom->copy()->addMonth()->endOfMonth()->format('Y-m-d')
                ]),
                'current' => $dateFrom->copy()->format('Y-m-d')
            ]
        ]);
    }
}
