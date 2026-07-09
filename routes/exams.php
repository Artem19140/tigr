<?php

use App\Http\Controllers\Web\Exam\ExamController;
use App\Http\Controllers\Web\Exam\ExamDocumentController;
use App\Http\Controllers\Web\Exam\ExamEnrollmentController;
use App\Http\Controllers\Web\Exam\ExamViewController;
use App\Http\Controllers\Web\Exam\MyExamController;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamType;

Route::resource('exams', ExamController::class)
    ->except('show')
    ->middleware(['meta'])
    ->where(['exam' => '[0-9]+']);

Route::get('exams/{exam}', [ExamViewController::class, 'show'])
    ->can('view', 'exam')
    ->where(['exam' => '[0-9]+']);

Route::get('my-exams', [MyExamController::class, 'index'])
    ->name('my-exams.index')
    ->can('conductAny', Exam::class);

Route::prefix('exams')
    ->middleware(['meta'])
    ->group(function () {

    Route::get('available', [ExamEnrollmentController::class, 'available'])
        ->can('create', Enrollment::class);

    Route::get('create/data', [ExamController::class, 'createData'])
        ->can('create', Exam::class);

    Route::get('types', function () {
        return ExamType::cached();
    });

    Route::middleware('can:examiner,exam')
        ->group(function () {

            Route::get('{exam}/check', [ExamViewController::class, 'check'])
                ->name('exam.show.check');

            Route::get('{exam}/conduct', [ExamViewController::class, 'conduct'])->name('exams.show.conduct');
            Route::put('{exam}/monitoring/protocol-comments', [ExamController::class, 'protocolComment']);

            Route::get('{exam}/documents/codes', [ExamDocumentController::class, 'codes'])
                ->name('exam.documents.codes');

            Route::get('{exam}/documents/codes/available', [ExamDocumentController::class, 'codesAvailable'])
                ->name('exam.documents.codes.available');
        });

    Route::get('{exam}/documents/results', [ExamDocumentController::class, 'results'])
        ->middleware('can:results,exam')
        ->name('exam.documents.results');
        
    Route::get('{exam}/documents/results/available', [ExamDocumentController::class, 'resultsAvailable'])
        ->middleware('can:results,exam');

    Route::get('{exam}/documents/protocol', [ExamDocumentController::class, 'protocol'])
        ->middleware('can:protocol,exam')
        ->name('exam.documents.protocol');
        
    Route::get('{exam}/documents/protocol/available', [ExamDocumentController::class, 'protocolAvailable'])
        ->middleware('can:protocol,exam');

    Route::get('{exam}/documents/list', [ExamDocumentController::class, 'list'])
        ->name('exam.documents.list')
        ->can('list', 'exam');

    Route::get('{exam}/documents/list/available', [ExamDocumentController::class, 'listAvailable'])
        ->can('list', 'exam');
    
});
