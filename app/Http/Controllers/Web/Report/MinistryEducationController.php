<?php

namespace App\Http\Controllers\Web\Report;

use App\Http\Requests\Report\MinistryEducationReportRequest;
use App\Modules\Report\EnsureMinistryEducationAvailable;
use App\Modules\Report\MinistryEducationReportGenerator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MinistryEducationController
{
    public function __construct(
        protected EnsureMinistryEducationAvailable $ensureGenerationAvailable
    ){}
    public function download(
        MinistryEducationReportRequest $request,
        MinistryEducationReportGenerator $ministryEducationReportGenerator
    ): StreamedResponse {
        $dateFrom = '';
        $dateTo = '';
        if ($request->validated('lastWeek')) {
            $dateFrom = Carbon::now()->subWeek()->startOfWeek();
            $dateTo = Carbon::now()->subWeek()->endOfWeek();
        } else {
            $dateFrom = Carbon::parse($request->validated('dateFrom'))->startOfDay();
            $dateTo = Carbon::parse($request->validated('dateTo'))->endOfDay();
        }

        $this->ensureGenerationAvailable->execute(
            $dateFrom,
            $dateTo
        );

        $fileName = 'Отчет_минобрнауки_'.$dateFrom->copy()->format('d.m.Y').'_'.$dateTo->copy()->format('d.m.Y').'.csv';

        return response()->streamDownload(function () use (
            $ministryEducationReportGenerator,
            $dateFrom,
            $dateTo
        ) {
            $ministryEducationReportGenerator->execute(
                $dateFrom,
                $dateTo
            );
        },
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    public function availability(
        MinistryEducationReportRequest $request
    ): JsonResponse {
        $dateFrom = '';
        $dateTo = '';

        if ($request->validated('lastWeek')) {
            $dateFrom = Carbon::now()->subWeek()->startOfWeek();
            $dateTo = Carbon::now()->subWeek()->endOfWeek();
        } else {
            $dateFrom = Carbon::parse($request->validated('dateFrom'))->startOfDay();
            $dateTo = Carbon::parse($request->validated('dateTo'))->endOfDay();
        }

        $this->ensureGenerationAvailable->execute(
            $dateFrom,
            $dateTo
        );

        return response()->json([
            'redirectUrl' => route('reports.ministry-education.download', [
                'lastWeek' => $request->validated('lastWeek'),
                'dateFrom' => $request->validated('dateFrom'),
                'dateTo' => $request->validated('dateTo'),
            ]),
        ]);
    }
}
