<?php

namespace App\Http\Controllers\Web\PlatformManage;

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
            abort(400, 'Лог недоступен');
        }

        return  $path;
    }

    public function downloadGitLog()
    {
        $logPath = '/var/www/tigr/git.log';

        if (!file_exists($logPath)) {
            Log::warning('Git log file not found');
            abort(400, 'Файл не найден');
        }

        return response()->download($logPath, 'git_log_' . now()->format('Y-m-d_His') . '.txt');
    }

    public function deleteDeployLog()
    {
        $logPath = '/var/www/tigr/git.log';

        if (!file_exists($logPath)) {
            Log::warning('Git log file not found');
            abort(400, 'Файл не найден');
        }

        file_put_contents($logPath, '');

        Log::info('Deploy logs succesfully cleared');
        
        return response()->noContent();
    }
}
