<?php

use App\Http\Controllers\Web\ExamDocument\CodesController;
use App\Http\Controllers\Web\ExamDocument\ListController;
use App\Http\Controllers\Web\ExamDocument\ProtocolController;
use App\Http\Controllers\Web\ExamDocument\ResultsController;
use App\Http\Controllers\Web\ExamDocument\ScoresController;
use App\Http\Controllers\Web\ExamDocument\StatementController;
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
})->name('exams.results');


Route::prefix('exams/{exam}/documents')
    ->middleware(['meta'])
    ->group(function () {

    Route::get('codes', [CodesController::class, 'generate'])
        ->can('codes', 'exam')
        ->name('exams.documents.codes');

    Route::get('codes/availability', [CodesController::class, 'availability'])
        ->can('codes', 'exam')
        ->name('exams.documents.codes.availability');

    Route::get('results', [ResultsController::class, 'generate'])
        ->can('results', 'exam')
        ->name('exams.documents.results');
        
    Route::get('results/availability', [ResultsController::class, 'availability'])
        ->name('exams.documents.results.availability')
        ->can('results', 'exam');

    Route::get('protocol', [ProtocolController::class, 'generate'])
        ->can('protocol', 'exam')
        ->name('exams.documents.protocol');
        
    Route::get('protocol/availability', [ProtocolController::class, 'availability'])
        ->name('exams.documents.protocol.availability')
        ->can('protocol', 'exam');

    Route::get('list', [ListController::class, 'generate'])
        ->name('exams.documents.list')
        ->can('list', 'exam');

    Route::get('list/availability', [ListController::class, 'availability'])
        ->name('exams.documents.list.availability')
        ->can('list', 'exam');


    /////////////////

    Route::get('statement/availability', [StatementController::class, 'availability'])
        ->name('exams.documents.statement.availability')
        ->can('results', 'exam');

    Route::get('statement', [StatementController::class, 'generate'])
        ->name('exams.documents.statement')
        ->can('results', 'exam');


    Route::get('scores/availability', [ScoresController::class, 'availability'])
        ->name('exams.documents.scores.availability')
        ->can('results', 'exam');

    Route::get('scores', [ScoresController::class, 'generate'])
        ->name('exams.documents.scores')
        ->can('results', 'exam');
});