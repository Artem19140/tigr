<?php

use App\Http\Controllers\Web\Exam\ExamController;
use App\Http\Controllers\Web\Exam\AvailableExamsController;
use App\Http\Controllers\Web\Exam\MyExamsController;

use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamType;

Route::resource('exams', ExamController::class)
    ->except('show')
    ->middleware(['meta'])
    ->where(['exam' => '[0-9]+']);


Route::get('my-exams', [MyExamsController::class, 'index'])
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

    Route::get('{exam}', [ExamController::class, 'show'])
        ->can('view', 'exam')
        ->name('exams.show')
        ->where(['exam' => '[0-9]+']);

    Route::put('{exam}/counters', [ExamController::class, 'updateCounters'])
        ->can('view', 'exam')
        ->name('exams.update.counters')
        ->where(['exam' => '[0-9]+']);


});
