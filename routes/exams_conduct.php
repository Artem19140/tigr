<?php

use App\Http\Controllers\Web\ExamConduct\AttemptManagementController;
use App\Http\Controllers\Web\ExamConduct\ExamConductController;
use App\Http\Controllers\Web\ExamConduct\SpeakingController;

Route::prefix('attempts')->middleware([
    'meta',
    'can:attempts.employee-access,attempt'
])
->group(function () {
    Route::delete('{attempt}', [AttemptManagementController::class, 'annul'])
        ->name('attempts.destroy');

    Route::get('{attempt}/speaking', [SpeakingController::class, 'show'])
        ->name('attempts.speaking.show');

    Route::post('{attempt}/speaking/finish', [SpeakingController::class, 'finish'])
        ->name('attempts.speaking.finish');

    Route::post('{attempt}/speaking/start', [SpeakingController::class, 'start'])
        ->name('attempts.speaking.start');
});

Route::prefix('exams')->middleware([
    'meta',
    'can:conduct,exam'
])
->group(function () {
    Route::get('{exam}/conduct', [ExamConductController::class, 'show'])
        ->name('exams.conduct');

    Route::get('{exam}/protocol-comments/edit', [ExamConductController::class, 'protocolCommentEdit'])
        ->name('exams.protocol-comments.edit');

    Route::put('{exam}/monitoring/protocol-comments', [ExamConductController::class, 'protocolCommentUpdate'])
        ->name('exams.protocol-comments.update');
});