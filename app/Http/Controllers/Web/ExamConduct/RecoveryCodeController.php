<?php

namespace App\Http\Controllers\Web\ExamConduct;

use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAccessCode;
use App\Modules\Attempt\ActiveAttemptRules;
use App\Modules\Enrollment\ExamCodeSaver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\ScopeBindings;

#[Middleware('auth:web')]
#[Authorize('conduct', 'exam')]
#[Prefix('exams')]

class RecoveryCodeController
{
    public function __construct(
        protected ExamCodeSaver $examCodeSaver,
        protected ActiveAttemptRules $rules
    ){}

    #[Get('{exam}/enrollments/{enrollment}/recovery-code', 'enrollments.show.recovery-codes')]
    #[ScopeBindings()]
    public function show(
        Exam $exam,
        Enrollment $enrollment
    ): JsonResponse{
        $this->ensureAvailabilty($enrollment);

        $recoveryCode = $enrollment->recoveryCode;  

        if( ! $recoveryCode?->isValid() ){
            $generationUrl = route('enrollments.recovery-codes', [
                'exam' => $enrollment->exam_id,
                'enrollment' => $enrollment
            ]);

            return response()->json([
                'code' => null,
                'generationUrl' => $generationUrl,
            ]);
        }

        return response()->json([
            'code' => $recoveryCode->code,
            'generationUrl' => null
        ]);

    }

    #[Post('{exam}/enrollments/{enrollment}/recovery-code', 'enrollments.recovery-codes')]
    #[ScopeBindings()]
    public function generation(
        Request $request,
        Exam $exam,
        Enrollment $enrollment
    ): JsonResponse {

        $request->validate([
            'reason' => ['required', 'string']
        ]);

        $this->ensureAvailabilty($enrollment);

        $recoveryCode = $enrollment->recoveryCode;  

        if( $recoveryCode?->isValid() ){
            return response()->json([
                'code' => $recoveryCode->code
            ]);
        }
 
        $newRecoveryCode = DB::transaction(function() use($enrollment, $request){
            $this->examCodeSaver->save(
                $enrollment,
                now()->addMinutes(ExamAccessCode::RECOVERY_CODE_TTL_MINUTES),
                ExamAccessCode::RECOVERY_TYPE,
                $request->input('reason')
            );

            $enrollment->refresh();

            $recoveryCode = $enrollment->recoveryCode;

            return $recoveryCode;
        });

        return response()->json([
            'code' => $newRecoveryCode->code
        ]);
    }
    
    protected function ensureAvailabilty(
        Enrollment $enrollment
    ): Attempt {
        $attempt = $enrollment->attempt;

        $result = $this->rules->check($attempt);

        if($result->isNotAvailable()){
            throw new BusinessException(
                $result->code()
            );
        }

        return $attempt;
    }
}