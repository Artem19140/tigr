<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Models\Enrollment;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PersonConfirmationController
{
    public function confirm()
    {
        $enrollmentId = session('enrollment_id');

        if(! $enrollmentId){
            return to_route('login');
        }

        $enrollment = Enrollment::find($enrollmentId);

        if(! $enrollment){
            Log::critical('no_enrollment_found', [
                'enrollment_id' => session('enrollment_id')
            ]);
            return to_route('login');
        }

        return Inertia::render('ExamSession/PersonConfirmation', [
            'fullName' => $enrollment->foreignNational->full_name,
            'dateBirth' => $enrollment->foreignNational->formattedDateBirth(),
            'confirmUrl' => route('exams-sessions.start-info', [
                'enrollment' => $enrollment
            ], false),
            'rejectUrl' => route('exams-sessions.confirmation-rejection', [], false)
        ]);
    }

    public function reject()
    {
        $enrollmentId = session('enrollment_id');

        if(! $enrollmentId){
            return to_route('login');
        }

        Log::warning('confirmation_rejection', [
            'enrollment_id' => $enrollmentId
        ]);

        session()->forget('enrollment_id');

        return to_route('login');
    }    
    
}
