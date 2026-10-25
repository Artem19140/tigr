<?php

namespace App\Http\Controllers\Web\ExamConduct;

use App\Enums\BusinessCode;
use App\Exceptions\BusinessException;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamCodesGenerator;
use App\Support\Audit;
use Carbon\Carbon;

class GenerateAttemptRecoveryCodeController
{
    public function show(
        Exam $exam,
        Enrollment $enrollment
    ){
        $attempt = $enrollment->attempt;

        if(! $attempt){          
            throw new BusinessException(
                BusinessCode::AttemptsNotExists
            );
        }
        
        if(! $attempt->isActive()){
            abort(400, 'Нет активной попытки экзамена');
        }

        return response()->json([
            'code' => $enrollment->examCode()
        ]);

    }
    public function generation(
        Exam $exam,
        Enrollment $enrollment,
        ExamCodesGenerator $codesGenerator,
        Audit $audit
    ){
        $attempt = $enrollment->attempt;

        if(! $attempt){          
            throw new BusinessException(
                BusinessCode::AttemptsNotExists
            );
        }
        
        if(! $attempt->isActive()){
            abort(400, 'Нет активной попытки экзамена');
        }

        if($enrollment->exam_code_expired_at?->isFuture() && $enrollment->exam_code){
            return response()->json([
                'code' => $enrollment->examCode()
            ]);
        }
        
        $enrollment->update([
            'exam_code_expired_at' => null,
            'exam_code_used_at'=> null
        ]);

        $codesGenerator->execute($exam);

        $enrollment->update([
            'exam_code_expired_at' => Carbon::now()->addMinutes(10)
        ]);
        
        $audit->log(
            'regenerated_exam_code', 
            $enrollment,
            [
                'code' => $enrollment->exam_code
            ]
        );

        return response()->json([
            'code' => $enrollment->examCode()
        ]);
    }
    
}
