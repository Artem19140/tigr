<?php

namespace App\Http\Controllers\Web\Report;

use App\Modules\Report\EnsureFlatTableAvailable;
use App\Modules\Report\EnsureFrdoGenerationAvailable;
use App\Modules\Report\EnsureMinistryEducationAvailable;
use App\Modules\Report\FlatTableGenerator;
use App\Modules\Report\FRDOReportsGenerator;
use App\Modules\Report\MinistryEducationReportGenerator;
use App\Http\Requests\Report\FlatTableRequest;
use App\Http\Requests\Report\FrdoReportRequest;
use App\Http\Requests\Report\MinistryEducationReportRequest;
use App\Navigation\ReportNavigation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        $date = Carbon::parse($request->validated('date'));
        $writer = $frdoGenerator->execute(
            $date,
            $type
        );
        $stringDate = $date->format('d.m.Y');
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

    public function availabilityFrdo(
        FrdoReportRequest $request,
        EnsureFrdoGenerationAvailable $ensureFrdoGenerationAvailable
    ): JsonResponse {
        $ensureFrdoGenerationAvailable->execute(
            $request->input('date'), 
            $request->input('type')
        );

        return response()->json([
            'redirectUrl' => route('reports.frdo.download', [
                'date' => $request->validated('date'),
                'type' => $request->validated('type'),
            ]),
        ]);
    }

    public function flatTable(
        FlatTableRequest $request,
        FlatTableGenerator $flatTableGenerator,
        EnsureFlatTableAvailable $ensureFlatTableAvailable
    ): StreamedResponse {
        $dateFrom = Carbon::parse($request->validated('dateFrom'));
        $dateTo = Carbon::parse($request->validated('dateTo'));

        $ensureFlatTableAvailable->execute(
            $dateFrom, 
            $dateTo
        );
        
        $fileName = "Плоская_таблица_{$dateFrom->format('d.m.Y')}_{$dateTo->format('d.m.Y')}.csv";

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

    public function availabilityFlatTable(
        FlatTableRequest $request,
        EnsureFlatTableAvailable $ensureFlatTableAvailable
    ): JsonResponse {

        $ensureFlatTableAvailable->execute(
            Carbon::parse($request->input('dateFrom')), 
            Carbon::parse($request->input('dateTo'))
        );

        return response()->json([
            'redirectUrl' => route('reports.flat-table.download', [
                'dateFrom' => $request->validated('dateFrom'),
                'dateTo' => $request->validated('dateTo'),
            ]),
        ]);
    }

    public function availabilityMinistryEducation(
        MinistryEducationReportRequest $request,
        EnsureMinistryEducationAvailable $ensureMinistryEducationAvailable
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

        $ensureMinistryEducationAvailable->execute(
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

    public function ministryEducation(
        MinistryEducationReportRequest $request,
        MinistryEducationReportGenerator $ministryEducationReportGenerator,
        EnsureMinistryEducationAvailable $ensureMinistryEducationAvailable
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

        $ensureMinistryEducationAvailable->execute(
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

    public function resolve(
        Request $request, 
        ReportNavigation $navigation
    ): RedirectResponse
    {
        $employee = $request->user();

        $allowedRoutes = $navigation->resolve($employee);

        if($allowedRoutes->isEmpty()){
            Log::warning('UNEXPECTED: reports route not resolved ', [
                'employee' => $employee->id,
                'route' => $request->route()
            ]);
            abort(403);
        }
        return redirect($allowedRoutes->first()['url']);
    }
}