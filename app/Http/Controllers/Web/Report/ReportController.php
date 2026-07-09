<?php

namespace App\Http\Controllers\Web\Report;

use App\Modules\Report\EnsureFrdoGenerationAvailable;
use App\Modules\Report\FlatTableGenerator;
use App\Modules\Report\FRDOReportsGenerator;
use App\Modules\Report\MinistryEducationReportGenerator;
use App\Http\Requests\Report\FlatTableRequest;
use App\Http\Requests\Report\FrdoReportRequest;
use App\Http\Requests\Report\MinistryEducationReportRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController
{
    public function frdo(
        FrdoReportRequest $request,
        FRDOReportsGenerator $frdoGenerator
    ): StreamedResponse {

        $type = $request->validated('type');
        $examDate = Carbon::parse($request->validated('examDate'));
        $writer = $frdoGenerator->execute(
            $examDate,
            $type
        );
        $stringDate = $examDate->format('d.m.Y');
        $fileName = $type === 'certificates' ? "Сертификаты_ФРДО_$stringDate.xlsx" : "Справки_ФРДО_$stringDate.xlsx";

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=$fileName",
            'Cache-Control' => 'max-age=0',
        ];

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, $headers);
    }

    public function availableFrdo(
        FrdoReportRequest $request,
        EnsureFrdoGenerationAvailable $ensureFrdoGenerationAvailable
    ): JsonResponse {
        $ensureFrdoGenerationAvailable->execute(
            $request->input('examDate'), 
            $request->input('type')
        );

        return response()->json([
            'redirectUrl' => route('reports.frdo.download', [
                'examDate' => $request->validated('examDate'),
                'type' => $request->validated('type'),
            ]),
        ]);
    }

    public function flatTable(
        FlatTableRequest $request,
        FlatTableGenerator $flatTableGenerator
    ): StreamedResponse {
        $dateFrom = Carbon::parse($request->validated('dateFrom'));
        $dateTo = Carbon::parse($request->validated('dateTo'));
        $fileName = 'Плоская_таблица'.'_'.$dateFrom->format('d.m.Y').'_'.$dateTo->format('d.m.Y').'.csv';

        return response()->streamDownload(function () use (
            $flatTableGenerator,
            $dateFrom,
            $dateTo
        ) {
            $flatTableGenerator->execute(
                $dateFrom,
                $dateTo
            );
        },
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
    }

    public function availableMinistryEducation(
        MinistryEducationReportRequest $request
    ): JsonResponse {
        return response()->json([
            'redirectUrl' => route('reports.ministry-education.download', [
                'lastWeek' => $request->validated('lastWeek'),
                'dateFrom' => $request->validated('dateFrom'),
                'dateTo' => $request->validated('dateTo'),
            ]),
        ]);
    }

    public function ministryEducation(
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

    public function resolve(Request $request)
    {
        $employee = $request->user();
        $route = match(true){
            $employee->can('reports.frdo') => 'reports.frdo',
            $employee->can('reports.flat-table') => 'reports.flat-table',
            $employee->can('reports.ministry-education') => 'reports.ministry-education',
            default => null
        };

        if(! $route){
            Log::warning('UNEXPECTED: reports route not resolved ', [
                'route' => $route
            ]);
            abort(403);
        }

        return redirect()->route($route);
    }
}
