<?php

namespace App\Http\Controllers\Web\Exam;

use App\Http\Resources\Exam\ExamShortResource;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamDocumentRules;
use App\Modules\ExamDocument\ExamStatementDataBuilder;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Where;

#[Middleware('auth:web')]
#[Where('exam', '[0-9]+')]

class ExamResultsController
{
    #[Get('exams/{exam}/results', 'exams.results')]
    #[Authorize('results', 'exam')]
    public function show(
        Exam $exam,
        ExamStatementDataBuilder $builder,
        ExamDocumentRules $rules
    ) {
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

        $headers = [];
        $rows = [];

        if($result->available){
            $dto = $builder->build($exam);

            $headers = $dto['headers'];
            $rows = $dto['rows'];

            $headers = array_values(
                array_filter($headers, fn ($header) => $header['shortVersion'] ?? false)
            );
            
            $rows = array_values(
                array_map(
                    fn ($row) => array_values(
                        array_filter($row, fn ($data) => $data['shortVersion'] ?? false)
                    ),
                    $rows
                )
            );
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
    }
    
}
