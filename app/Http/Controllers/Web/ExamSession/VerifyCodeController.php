<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Http\Requests\Exam\VerifyCodeRequest;
use App\Modules\ExamSession\ExamCodeVerifier;

class VerifyCodeController
{
    public function verify(
        VerifyCodeRequest $request,
        ExamCodeVerifier $examCodeVerifier
    ) {
        $enrollment = $examCodeVerifier->execute(
            $request->validated('code')
        );

        session([
            'enrollment_id' => $enrollment->id
        ]);

        return redirect()->route('exams-sessions.confirmation');
    }
}