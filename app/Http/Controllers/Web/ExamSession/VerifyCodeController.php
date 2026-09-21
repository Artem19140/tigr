<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Http\Requests\Exam\VerifyCodeRequest;
use App\Modules\Attempt\CreateAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VerifyCodeController
{
    public function verify(
        VerifyCodeRequest $request,
        CreateAttempt $createAttempt
    ): RedirectResponse {
        $attempt = $createAttempt->execute(
            $request->validated('code')
        );

        Auth::guard('foreignNationals')
            ->login($attempt->foreignNational);

        $request->session()->regenerate();

        return redirect()->route('attempts.show', [
            'attempt' => $attempt->id,
        ]);
    }
}
