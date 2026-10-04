<?php

use App\Http\Controllers\Web\Exam\ExamController;
use App\Http\Controllers\Web\Exam\AvailableExamsController;
use App\Http\Controllers\Web\Exam\MyExamController;
use App\Http\Controllers\Web\ExamDocument\CodesController;
use App\Http\Controllers\Web\ExamDocument\ListController;
use App\Http\Controllers\Web\ExamDocument\ProtocolController;
use App\Http\Controllers\Web\ExamDocument\ResultsController;
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

    Route::get('available', [AvailableExamsController::class, 'index'])
        ->can('create', Enrollment::class);

    Route::get('types', function () {
        return ExamType::cached();
    });

    Route::get('{exam}/documents/codes', [CodesController::class, 'generate'])
        ->can('codes', 'exam')
        ->name('exams.documents.codes');

    Route::get('{exam}/documents/codes/availability', [CodesController::class, 'availability'])
        ->can('codes', 'exam')
        ->name('exams.documents.codes.availability');

    Route::get('{exam}/documents/results', [ResultsController::class, 'generate'])
        ->can('results', 'exam')
        ->name('exams.documents.results');
        
    Route::get('{exam}/documents/results/availability', [ResultsController::class, 'availability'])
        ->name('exams.documents.results.availability')
        ->can('results', 'exam');

    Route::get('{exam}/documents/protocol', [ProtocolController::class, 'generate'])
        ->can('protocol', 'exam')
        ->name('exams.documents.protocol');
        
    Route::get('{exam}/documents/protocol/availability', [ProtocolController::class, 'availability'])
        ->name('exams.documents.protocol.availability')
        ->can('protocol', 'exam');

    Route::get('{exam}/documents/list', [ListController::class, 'generate'])
        ->name('exams.documents.list')
        ->can('list', 'exam');

    Route::get('{exam}/documents/list/availability', [ListController::class, 'availability'])
        ->name('exams.documents.list.availability')
        ->can('list', 'exam');
});
