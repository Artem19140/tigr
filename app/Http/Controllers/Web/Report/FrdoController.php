<?php

namespace App\Http\Controllers\Web\Report;

use App\Http\Requests\Report\FrdoReportRequest;
use App\Modules\Report\EnsureFrdoGenerationAvailable;
use App\Modules\Report\FRDOReportsGenerator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FrdoController
{
    public function __construct(
        protected EnsureFrdoGenerationAvailable $ensureGenerationAvailable,
    ){}
    public function download(
        FrdoReportRequest $request,
        FRDOReportsGenerator $frdoGenerator
    ): StreamedResponse {

        $type = $request->validated('type');
        $date = Carbon::parse($request->validated('date'));

        $this->ensureGenerationAvailable->execute(
            $request->input('date'), 
            $request->input('type')
        );

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

    public function availability(
        FrdoReportRequest $request
    ): JsonResponse {
        $this->ensureGenerationAvailable->execute(
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
}
