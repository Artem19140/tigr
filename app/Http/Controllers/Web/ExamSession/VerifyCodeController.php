<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Http\Requests\Exam\VerifyCodeRequest;
use App\Modules\ExamSession\ExamCodeVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VerifyCodeController
{
    public function verify(
        VerifyCodeRequest $request,
        ExamCodeVerifier $examCodeVerifier
    ): RedirectResponse {
        $enrollment = $examCodeVerifier->execute(
            $request->validated('code')
        );

        if($enrollment->attempt?->isActive()){
            Auth::guard('foreignNationals')
                ->login($enrollment->foreignNational);
                
            $enrollment->recoveryCode
                ->makeUsed();

            return redirect()->route('attempts.show', [
                'attempt' => $enrollment->attempt->id
            ]);
        }

        session([
            'enrollment_id' => $enrollment->id
        ]);

        return redirect()->route('exams-sessions.confirmation.show');
    }
    
}