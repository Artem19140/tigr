<?php

use App\Http\Resources\Exam\ExamShortResource;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamDocumentRules;
use App\Modules\ExamDocument\ExamStatementDataBuilder;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('exams/{exam}/results', function(
    Exam $exam,
    ExamStatementDataBuilder $builder,
    ExamDocumentRules $rules
){
    $exam->loadExists([
        'enrollments',
        'attempts',
        'enrollments as enrollments_with_no_attempts_exists' => function($query){
            return $query->whereDoesntHave('attempt');
        },
        'attempts as active_attempts_exists' => function ($query) {
            $query->active();
        },
        'attempts as unreviewed_attempts_exists' => function ($query) {
            return $query->unreviewed();
        }
    ]);

    $result = $rules->results($exam);


    $dto = $builder->build($exam);
    $headers = [];
    $rows = [];

    if($result->available){
        $headers = $dto['headers'];
        $rows = $dto['rows'];
    }

    return Inertia::render('Exam/Results', [
        'exam' => new ExamShortResource($exam),
        'headers' => $headers,
        'rows' => $rows,
        'backUrl' => route('exams.show', [
            'exam' => $exam
        ], false),
        'availability' => [
            'available' => $result->available,
            'reason' => $result->message()
        ]
    ]);
})->name('exams.results');