<?php

use App\Enums\EmployeeRole;
use App\Http\Controllers\Web\Exam\ExamCheckingController;
use App\Http\Controllers\Web\Exam\ExamController;
use App\Http\Controllers\Web\Exam\ExamDocumentController;
use App\Http\Controllers\Web\Exam\ExamEnrollmentController;
use App\Http\Controllers\Web\Exam\ExamMonitoringController;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamType;
use App\Support\AppMiddleware;

Route::apiResource('exams', ExamController::class)->where(['exam' => '[0-9]+']);

Route::prefix('exams')->group(function () {

    Route::get('available', [ExamEnrollmentController::class, 'available'])
        ->can('create', Enrollment::class);

    Route::get('schedule', [ExamController::class, 'schedule'])
        ->name('exams.schedule')
        ->can('viewAny', Exam::class);

    Route::get('create/data', [ExamController::class, 'createData'])
        ->can('create', Exam::class);

    Route::get('types', function () {
        return ExamType::cached();
    });

    Route::middleware([
        AppMiddleware::EMPLOYEE_HAS_ANY_ROLE. ':' . EmployeeRole::implode(EmployeeRole::PlatformAdmin, EmployeeRole::Examiner)
    ])
        ->group(function () {
            Route::get('monitoring', [ExamMonitoringController::class, 'index'])->name('exams.monitoring');
            Route::get('checking', [ExamCheckingController::class, 'index']);
        });

    Route::middleware('can:examiner,exam')
        ->group(function () {
            Route::get('{exam}/checking', [ExamCheckingController::class, 'show']);

            Route::get('{exam}/monitoring', [ExamMonitoringController::class, 'show']);
            Route::put('{exam}/monitoring/protocol-comments', [ExamMonitoringController::class, 'protocolComment']);

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
