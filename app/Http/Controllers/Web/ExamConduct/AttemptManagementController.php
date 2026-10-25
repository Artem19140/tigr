<?php

namespace App\Http\Controllers\Web\ExamConduct;

use App\Models\Attempt;
use App\Modules\ExamConduct\AnnulAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttemptManagementController
{
    public function annulation(
        Request $request,
        Attempt $attempt,
        AnnulAttempt $annulAttempt
    ): RedirectResponse {

        $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $annulAttempt->execute(
            $attempt, 
            $request->input('reason'), 
            $request->user()
        );

        return redirect()->route('exams.conduct', [
            'exam' => $attempt->exam_id
        ]);
    }
}