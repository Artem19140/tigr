<?php

namespace App\Http\Controllers\Web\ExamConduct;

use App\Models\Attempt;
use App\Modules\Attempt\AnnulAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttemptManagementController
{
    public function annul(
        Request $request,
        Attempt $attempt,
        AnnulAttempt $annulAttempt
    ): RedirectResponse {

        $request->validate([
            'annulledReason' => ['required', 'string'],
        ]);

        $annulAttempt->execute(
            $attempt, 
            $request->input('annulledReason'), 
            $request->user()
        );

        return redirect()->route('exams.conduct', [
            'exam' => $attempt->exam_id
        ]);
    }
}