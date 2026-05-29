<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Exceptions\BusinessException;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogsController
{
    public function available(
        Request $request
    ): JsonResponse {
        $request->validate([
            'date' => ['required', 'date']
        ]);
        
        $this->getPathOrFail($request->input('date'));

        return response()->json([
            'redirectUrl' => route('logs.download', [
                'date' => $request->input('date')
            ])
        ]);
    }

    public function download(
        Request $request
    )
    {
        $request->validate([
            'date' => ['required', 'date']
        ]);
        $path = $this->getPathOrFail($request->input('date'));
        return response()->download($path);
    }


    protected function getPathOrFail(string $date):string
    {
        $formattedDate = Carbon::parse($date)->copy()->format('Y-m-d');
        $path = storage_path("logs/laravel-{$formattedDate}.log");

        if(!file_exists($path)){
            throw new BusinessException('Лог недоступен');
        }

        return  $path;
    }
}
