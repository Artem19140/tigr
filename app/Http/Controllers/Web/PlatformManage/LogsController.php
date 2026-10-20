<?php

namespace App\Http\Controllers\Web\PlatformManage;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Enrollment;
use App\Modules\ExamSession\ExamVariantGenerator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogsController
{
    public function __construct(
        protected ExamVariantGenerator $generator
    ){}
    
    public function available(
        Request $request
    ): JsonResponse {
        

        $this->er();
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
            Log::critical('Git log file not found');
            abort(400, 'Файл не найден');
        }

        return response()->download($logPath, 'git_log_' . now()->format('Y-m-d_His') . '.txt');
    }
    
    protected function er()
    {
        DB::transaction(function(){
            $enrollment = Enrollment::find(44);
            if($enrollment->attempt()->exists()){
                return ;
            }
            $time = Carbon::parse('2026-10-20 10:16');
            $attempt = Attempt::create([
                'started_at' => $time,
                'expired_at' => $time->copy()->addMinutes($enrollment->exam->duration()),
                'finished_at' => Carbon::parse('2026-10-20 10:26'),
                'reviewed_at' => Carbon::parse('2026-10-20 10:26'),
                'is_passed' => false,
                'enrollment_id' => $enrollment->id,
                'foreign_national_id' => $enrollment->foreign_national_id,
                'exam_id' => $enrollment->exam_id
            ]);
            $variant = $this->generator->execute($enrollment->exam, $attempt);

            AttemptAnswer::insert($variant);

            $attempt->save();
            Log::info('Everything is ok' . $enrollment->id);
        });
    }
}
