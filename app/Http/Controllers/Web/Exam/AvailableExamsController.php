<?php

namespace App\Http\Controllers\Web\Exam;

use App\Models\Enrollment;
use App\Modules\Exam\GetAvailableExams;
use App\Http\Requests\Enrollment\EnrollmentAvailableRequest;
use App\Models\Exam;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Spatie\RouteAttributes\Attributes\Get;

#[Middleware('auth:web')] 

class AvailableExamsController
{
    #[Get('exams/available', 'exams.index.available')]
    #[Authorize('create', Enrollment::class)]
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
