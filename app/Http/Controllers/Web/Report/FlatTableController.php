<?php

namespace App\Http\Controllers\Web\Report;

use App\Http\Requests\Report\FlatTableRequest;
use App\Modules\Report\EnsureFlatTableAvailable;
use App\Modules\Report\FlatTableGenerator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FlatTableController
{
    public function __construct(
        protected EnsureFlatTableAvailable $ensureGenerationAvailable
    ){}
    
    public function download(
        FlatTableRequest $request,
        FlatTableGenerator $flatTableGenerator
    ): StreamedResponse {
        $dateFrom = Carbon::parse($request->validated('dateFrom'));
        $dateTo = Carbon::parse($request->validated('dateTo'));

        $this->ensureGenerationAvailable->execute(
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

    public function availability(
        FlatTableRequest $request
    ): JsonResponse {

        $this->ensureGenerationAvailable->execute(
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
}
