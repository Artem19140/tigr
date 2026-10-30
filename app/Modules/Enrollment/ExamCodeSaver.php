<?php

namespace App\Modules\Enrollment;

use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Log;

class ExamCodeSaver
{
    public function save(
        Enrollment $enrollment,
        string $code,
        Carbon $expiresAt,
        string $type
    ) {
        $retries = 0;
        while (true) {
            try {
                $enrollment->examCode()
                    ->create([
                        'code' => $code,
                        'code_snapshot' => $code,
                        'expires_at' => $expiresAt,
                        'type' => $type
                    ]);

                return;
            } catch (UniqueConstraintViolationException $e) {
                $retries ++;
                Log::info('Unique query exception during exam codes generation', [
                    'enrollment_id' => $enrollment->id,
                    'retries' => $retries
                ]);

                if($retries > 9){
                    throw new \RuntimeException('Too many retries exam codes generation');
                }
            }
        }
    }
    
}