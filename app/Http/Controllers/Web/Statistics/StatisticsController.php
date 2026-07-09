<?php

namespace App\Http\Controllers\Web\Statistics;

use App\Http\Requests\Statistics\StatisticsRequest;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\ForeignNational;
use App\Support\Audit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class StatisticsController
{
    public function __construct(
        protected Audit $audit
    ) {}

    public function index(StatisticsRequest $request): JsonResponse
    {

        $from = Carbon::parse($request->validated('dateFrom'))->copy()->startOfDay();
        $to = Carbon::parse($request->validated('dateTo'))->copy()->endOfDay();

        $examsCount = Exam::query()
            ->where('begin_time', '>=', $from)
            ->where('begin_time', '<=', $to)
            ->notCancelled()
            ->count();

        $attemptsTakersCount = ForeignNational::query()
            ->whereHas('attempts', function (Builder $query) use ($from, $to) {
                $query->whereBetween('started_at', [
                    $from,
                    $to,
                ]);
            })->count();

        $attemptsQuery = Attempt::query()
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to);

        $attemptsCount = (clone $attemptsQuery)->count();

        $successfulAttemptsCount = (clone $attemptsQuery)
            ->where('is_passed', true)
            ->count();

        $failedAttemptsCount = (clone $attemptsQuery)
            ->where('is_passed', false)
            ->whereNull('annulled_at')
            ->count();

        $annulledAttemptsCount = (clone $attemptsQuery)
            ->whereNotNull('annulled_at')
            ->count();
        $statistics = [
            'examsCount' => $examsCount,
            'attemptsCount' => $attemptsCount,
            'attemptsTakersCount' => $attemptsTakersCount,
            'failedAttemptsCount' => $failedAttemptsCount,
            'successfulAttemptsCount' => $successfulAttemptsCount,
            'annulledAttemptsCount' => $annulledAttemptsCount,
        ];

        $this->audit->log('export', 'statistics', [
            ...$statistics,
            'period' => [
                'from' => $from,
                'to' => $to
            ]
        ]);
        return response()->json($statistics);
    }
}
