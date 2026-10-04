<?php

namespace App\Http\Controllers\Web\Exam;

use App\Modules\Exam\GetAvailableExams;
use App\Http\Requests\Enrollment\EnrollmentAvailableRequest;
use App\Models\Exam;

class AvailableExamsController
{
    public function index(
        EnrollmentAvailableRequest $request,
        GetAvailableExams $getAvailableExams
    ) {

        $exams = $getAvailableExams->execute(
            $request->validated('examTypeId'),
            $request->validated('foreignNationalId')
        );
       
        return $exams->map(function (Exam $exam) {
            return [
                'id' => $exam->id,
                'beginTime' => $exam->beginTimeLocal()->format('H:i d.m.Y'),
            ];
        });
    }
}
