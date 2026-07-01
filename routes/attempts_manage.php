<?php

use App\Http\Controllers\Web\Attempt\AttemptAnswerController;
use App\Http\Controllers\Web\Attempt\AttemptCheckingController;
use App\Http\Controllers\Web\Attempt\AttemptController;
use App\Http\Controllers\Web\Attempt\AttemptSpeakingController;
use App\Http\Controllers\Web\Attempt\AttemptViolationController;

Route::prefix('attempts')->middleware([
    'meta',
    'can:attempts.employee-access,attempt'
])
->group(function () {
    Route::delete('{attempt}', [AttemptController::class, 'annul'])->name('attempts.destroy');

    Route::get('{attempt}/checking', [AttemptCheckingController::class, 'show'])
        ->name('attempts.checking');
        
    Route::post('{attempt}/checking/finish', [AttemptCheckingController::class, 'finish'])
        ->name('attempts.checking.finish');

    Route::get('{attempt}/speaking', [AttemptSpeakingController::class, 'show'])
        ->name('attempts.speaking.show');

    Route::post('{attempt}/speaking/finish', [AttemptSpeakingController::class, 'finish'])
        ->name('attempts.speaking.finish');

    Route::post('{attempt}/speaking/start', [AttemptSpeakingController::class, 'start'])
        ->name('attempts.speaking.start');

    Route::get('{attempt}/violations', [AttemptViolationController::class, 'index'])
        ->name('attempts.violations.index');

    Route::post('{attempt}/violations', [AttemptViolationController::class, 'store'])
        ->name('attempts.violations.store');

    Route::delete('{attempt}/violations/{violation}', [AttemptViolationController::class, 'destroy'])
        ->scopeBindings()
        ->name('attempts.violations.destroy');

    Route::patch('{attempt}/violations/{violation}', [AttemptViolationController::class, 'update'])
        ->scopeBindings()
        ->name('attempts.violations.update');

    Route::put('{attempt}/answers/{attemptAnswer}/rate', [AttemptAnswerController::class, 'rate'])
        ->scopeBindings();
});
;
