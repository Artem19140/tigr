<?php

namespace App\Http\Controllers\Web\ExamConduct;

use App\Enums\BusinessCode;
use App\Exceptions\BusinessException;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAccessCode;
use App\Modules\Enrollment\ExamCodeSaver;
use App\Support\Audit;
use App\Support\CodeGenerator;

class GenerateRecoveryCodeController
{
    public function __construct(
        protected ExamCodeSaver $examCodeSaver,
        protected CodeGenerator $codeGenerator
    ){}
    
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
            'code' => $enrollment->recoveryCode->code
        ]);

    }
    public function generation(
        Exam $exam,
        Enrollment $enrollment,
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

        if($enrollment->recoveryCode){
            return response()->json([
                'code' => $enrollment->examCode()
            ]);
        }
        
        $this->examCodeSaver->save(
            $enrollment,
            $this->codeGenerator->generate(),
            now()->addMinutes(ExamAccessCode::RECOVERY_CODE_TTL_MINUTES),
            ExamAccessCode::RECOVERY_TYPE
        );
        
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
