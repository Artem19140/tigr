<?php

use App\Http\Controllers\Web\Exam\ExamController;
use App\Http\Controllers\Web\Exam\ExamDocumentController;
use App\Http\Controllers\Web\Exam\ExamEnrollmentController;
use App\Http\Controllers\Web\Exam\MyExamController;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamType;

Route::resource('exams', ExamController::class)
    ->except('show')
    ->middleware(['meta'])
    ->where(['exam' => '[0-9]+']);

Route::get('exams/{exam}', [ExamController::class, 'show'])
    ->can('view', 'exam')
    ->name('exams.show')
    ->where(['exam' => '[0-9]+']);

Route::get('my-exams', [MyExamController::class, 'index'])
    ->name('my-exams.index')
    ->can('conductAny', Exam::class);

Route::prefix('exams')
    ->middleware(['meta'])
    ->group(function () {

    Route::get('available', [ExamEnrollmentController::class, 'available'])
        ->can('create', Enrollment::class);

    Route::get('types', function () {
        return ExamType::cached();
    });

    Route::get('{exam}/documents/codes', [ExamDocumentController::class, 'codes'])
        ->can('codes', 'exam')
        ->name('exams.documents.codes');

    Route::get('{exam}/documents/codes/availability', [ExamDocumentController::class, 'codesAvailable'])
        ->can('codes', 'exam')
        ->name('exams.documents.codes.availability');

    Route::get('{exam}/documents/results', [ExamDocumentController::class, 'results'])
        ->can('results', 'exam')
        ->name('exams.documents.results');
        
    Route::get('{exam}/documents/results/availability', [ExamDocumentController::class, 'resultsAvailable'])
        ->name('exams.documents.results.availability')
        ->can('results', 'exam');

    Route::get('{exam}/documents/protocol', [ExamDocumentController::class, 'protocol'])
        ->can('protocol', 'exam')
        ->name('exams.documents.protocol');
        
    Route::get('{exam}/documents/protocol/availability', [ExamDocumentController::class, 'protocolAvailable'])
        ->name('exams.documents.protocol.availability')
        ->can('protocol', 'exam');

    Route::get('{exam}/documents/list', [ExamDocumentController::class, 'list'])
        ->name('exams.documents.list')
        ->can('list', 'exam');

    Route::get('{exam}/documents/list/availability', [ExamDocumentController::class, 'listAvailable'])
        ->name('exams.documents.list.availability')
        ->can('list', 'exam');
});
