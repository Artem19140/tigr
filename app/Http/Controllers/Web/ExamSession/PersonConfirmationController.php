<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PersonConfirmationController
{
    public function confirmationView() 
    {
        $enrollmentId = session('enrollment_id');

        if(! $enrollmentId){
            return to_route('login');
        }

        $enrollment = Enrollment::find($enrollmentId);

        if(! $enrollment){
            Log::warning('no_enrollment_found', [
                'enrollment_id' => session('enrollment_id')
                
            ]);

            return to_route('login');
        }

        return Inertia::render('ExamSession/PersonConfirmation', [
            'fullName' => $enrollment->foreignNational->full_name,
            'dateBirth' => $enrollment->foreignNational->formattedDateBirth(),
            'confirmUrl' => route('exams-sessions.confirmation', [], false),
            'rejectUrl' => route('exams-sessions.confirmation-rejection', [], false)
        ]);
    }
    
    public function confirmation(
        Request $request
    ) {
        $enrollmentId = session('enrollment_id');

        if(! $enrollmentId){
            return to_route('login');
        }

        $enrollment = Enrollment::find($enrollmentId);

        if(! $enrollment){
            Log::warning('No enrollment found', [
                'enrollment_id' => session('enrollment_id')
                
            ]);

            return to_route('login');
        }

        Auth::guard('foreignNationals')
            ->login($enrollment->foreignNational);

        session()->forget('enrollment_id');

        $request->session()->regenerate();

        activity('person_confirmation')
            ->performedOn($enrollment)
            ->withProperties([
                'code' => $enrollment->exam_code
            ]);

        return to_route('exams-sessions.sound-setting', [
            'enrollment' => $enrollment
        ]);
    }

    public function rejection(): RedirectResponse{
        $enrollmentId = session('enrollment_id');

        if(! $enrollmentId){
            return to_route('login');
        }

        activity('person_confirmation_rejection')
            ->withProperties([
                'code' => $enrollmentId
            ]);

        session()->forget('enrollment_id');

        return to_route('login');
    }    
}
