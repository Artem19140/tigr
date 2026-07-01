<?php

namespace App\Http\Controllers\Web\PlatformManage;

use App\Exceptions\BusinessException;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogsController
{
    public function available(
        Request $request
    ): JsonResponse {
        $request->validate([
            'date' => ['required', 'date'],
            'type' => ['sometimes', 'string']
        ]);
        
        $this->getPathOrFail(
            $request->input('date'), 
            $request->input('type')
        );
        
        return response()->json([
            'redirectUrl' => route('logs.download', [
                'date' => $request->input('date'),
                'type' =>  $request->input('type'),
            ])
        ]);
    }

    public function download(
        Request $request
    )
    {
        $request->validate([
            'date' => ['required', 'date'],
            'type' => ['sometimes']
        ]);

        $path = $this->getPathOrFail(
            $request->input('date'), 
            $request->input('type')
        );

    
        return response()->download($path);
    }


    protected function getPathOrFail(
        string $date,
        ?string $type = null
    ):string
    {

        $formattedDate = Carbon::parse($date)->copy()->format('Y-m-d');

        $path = storage_path($type ? "logs/audit/audit-{$formattedDate}.log" : "logs/laravel-{$formattedDate}.log");
        
        if(! file_exists($path)){
            throw new BusinessException('Лог недоступен');
        }

        return  $path;
    }

    public function downloadGitLog()
    {
        $logPath = '/var/www/tigr/git.log';

        if (!file_exists($logPath)) {
            Log::critical('Git log file not found');
            throw new BusinessException('Файл не найден');
        }

        return response()->download($logPath, 'git_log_' . now()->format('Y-m-d_His') . '.txt');
    }
}
