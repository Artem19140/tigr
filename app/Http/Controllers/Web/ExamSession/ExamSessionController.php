<?php

namespace App\Http\Controllers\Web\ExamSession;

use App\Models\Enrollment;
use App\Models\Exam;
use App\Modules\ExamSession\StartExamSession;
use App\Modules\Shared\ExamSettings;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ExamSessionController
{

    public function startInfo(
        Enrollment $enrollment
    ) {
        $exam = Exam::with([
            'type',
        ])->find($enrollment->exam_id);

        if($enrollment->attempt()->exists()){
            Log::info('Redirecting to attempt from start info exam session', [
                'enrollment_id' => $enrollment->id,
                'attempt_id' => $enrollment->attempt->id,
                'attempt_status' => $enrollment->attempt->status
            ]);

            return redirect()->route('attempts.show', [
                'attempt' => $enrollment->attempt?->id,
            ]);
        }

        if(!$exam){
            Log::warning('exam_not_found',[
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
                'name' => $exam->fullName()
            ],
            'fullName' => $enrollment->foreignNational->full_name_short,
            'startUrl' => route('exams-sessions.start', [
                'enrollment' => $enrollment
            ], false)
        ]);
    }
    public function start(
        Enrollment $enrollment,
        StartExamSession $startExamSession
    ) {
        if($enrollment->attempt()->exists()){
            Log::info('Redirecting to attempt from starting exam session', [
                'enrollment_id' => $enrollment->id,
                'attempt_id' => $enrollment->attempt->id,
                'attempt_status' => $enrollment->attempt->status
            ]);
            return redirect()->route('attempts.show', [
                'attempt' => $enrollment->attempt?->id,
            ]);
        }

        $attempt = $startExamSession->execute(
            $enrollment
        );

        return redirect()->route('attempts.show', [
            'attempt' => $attempt->id,
        ]);
    }
    
}
