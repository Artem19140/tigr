<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Models\Enrollment;
use App\Models\Exam;
use App\Modules\ExamSession\StartExamSession;
use App\Modules\Shared\ExamSettings;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ExamSessionController
{

    public function startInfo(
        Enrollment $enrollment
    ) {
        $this->authorize($enrollment);

        $exam = Exam::with([
            'type',
        ])->find($enrollment->exam_id);

        if(!$exam){
            Log::critical('exam_not_found',[
                'enrollment_id' => $enrollment->id
            ]);

            throw new ModelNotFoundException();
        }

        

        return Inertia::render('ExamSession/StartInfo', [
            'exam' => [
                'duration' => $exam->duration(),
                'minMark' => $exam->type->min_mark,
                'tasksCount' => $exam->tasksCount(),
                'minTimeFromStartToFinish' => ExamSettings::attemptMinDurationMinutes(),
                'name' => $exam->type->name
            ],
            'fullName' => $enrollment->foreignNational->full_name_short,
            'startUrl' => route('exams-sessions.start', [
                'enrollment' => $enrollment
            ], false)
        ]);
    }
    public function start(
        StartExamSession $createAttempt,
        Enrollment $enrollment,
        Request $request
    ) {
        $this->authorize($enrollment);

        $attempt = $createAttempt->execute(
            $enrollment
        );

        Auth::guard('foreignNationals')
            ->login($attempt->foreignNational);

        $request->session()->regenerate();

        session()->forget('enrollment_id');

        return redirect()->route('attempts.show', [
            'attempt' => $attempt->id,
        ]);
    }

    protected function authorize(
        Enrollment $enrollment
    ) {
        $sessionEnrollmentId = session('enrollment_id');

        if(! $sessionEnrollmentId){
            return to_route('login');
        }

        if($sessionEnrollmentId === $enrollment->id){
            return ;
        }

        Log::warning('Enrollment session mismatch',[
            'enrollment_id' => $enrollment->id,
            'session_enrollment_id' => session('enrollment_id')
        ]);

        abort(404);
    }
    
}
