<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Http\Requests\Exam\VerifyCodeRequest;
use App\Modules\ExamSession\ExamCodeVerifier;
use App\Support\Audit;

class VerifyCodeController
{
    public function verify(
        VerifyCodeRequest $request,
        ExamCodeVerifier $examCodeVerifier,
        Audit $audit
    ) {
        $enrollment = $examCodeVerifier->execute(
            $request->validated('code')
        );

        session([
            'enrollment_id' => $enrollment->id
        ]);

        $audit->log(
            'Session enrollment_id set', 
            'enrollment', 
            [
                'enrollment_id' => $enrollment->id
            ]);

        return redirect()->route('exams-sessions.confirmation.show');
    }
}