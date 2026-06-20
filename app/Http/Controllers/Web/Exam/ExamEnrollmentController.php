<?php

namespace App\Http\Controllers\Web\Exam;

use App\Modules\Exam\GetAvailableExams;
use App\Http\Requests\Enrollment\EnrollmentAvailableRequest;
use App\Models\Exam;
use App\Support\CenterIsolationCheck;

class ExamEnrollmentController
{
    public function available(
        EnrollmentAvailableRequest $request,
        GetAvailableExams $getAvailableExams
    ) {

        $exams = $getAvailableExams->execute(
            $request->validated('examTypeId'),
            $request->validated('foreignNationalId')
        );

        CenterIsolationCheck::check($exams);
        
        return $exams->map(function (Exam $exam) {
            return [
                'id' => $exam->id,
                'beginTime' => $exam->begin_time_local->format('H:i d.m.Y'),
            ];
        });
    }
}
