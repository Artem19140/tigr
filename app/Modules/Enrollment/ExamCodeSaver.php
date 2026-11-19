<?php

namespace App\Modules\Enrollment;

use App\Models\Enrollment;
use App\Models\ExamAccessCode;
use App\Support\CodeGenerator;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Log;

class ExamCodeSaver
{
    public function __construct(
        protected CodeGenerator $codeGenerator
    ){}
    
    public function save(
        Enrollment $enrollment,
        Carbon $expiresAt,
        string $type,
        string | null $recoveryReason = null
    ) {
        $retries = 0;

        while (true) {
            try {
                $code = $this->codeGenerator->generate();
                
                ExamAccessCode::create([
                    'enrollment_id' => $enrollment->id,
                    'code' => $code,
                    'code_snapshot' => $code,
                    'expires_at' => $expiresAt,
                    'type' => $type,
                    'recovery_reason' => $recoveryReason
                ]);

                return;
            } catch (UniqueConstraintViolationException $e) {
                $retries ++;
                
                Log::info('Unique query exception during exam codes generation', [
                    'enrollment_id' => $enrollment->id,
                    'retries' => $retries
                ]);

                if($retries >= ExamAccessCode::MAX_RETRIES_GENERATION_COUNT){
                    throw new \RuntimeException('Too many retries exam codes generation');
                }
            }
        }
    }
    
}