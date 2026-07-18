<?php

use App\Http\Controllers\Web\Attempt\AttemptAnswerController;
use App\Http\Controllers\Web\Attempt\AttemptController;
use App\Http\Controllers\Web\Exam\ExamController;
use App\Support\AppMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('attempts')
    ->middleware([
        'meta',
        'auth:foreignNationals',
        AppMiddleware::ENSURE_ATTEMPT_VALID_STATUS,
    ])
    //->can('attempts.foreign-national-access:', 'attempt')
    ->group(function () {
        Route::put('{attempt}/finish', [AttemptController::class, 'finish'])
            ->name('attempts.finish');

        Route::get('{attempt}', [AttemptController::class, 'show'])
            ->name('attempts.show');

        Route::put('{attempt}', [AttemptController::class, 'start'])
            ->name('attempts.start');

        Route::put('{attempt}/answers/{attemptAnswer}', [AttemptAnswerController::class, 'update'])
            ->scopeBindings()
            ->name('attempts.answers.update');

        Route::put('{attempt}/answers/{attemptAnswer}/audio', [AttemptAnswerController::class, 'audioPlayed'])
            ->scopeBindings()
            ->name('attempts.answers.update.audio');
    });

Route::middleware([
    'meta',
    'guest:web,foreignNationals'
])->group(function () {
    Route::inertia('attempts/finish', 'Attempt/AfterAttempt')
        ->name('attempts.finish.after');

    Route::post('exam-codes/verify', [ExamController::class, 'verifyCode'])
        ->middleware(['throttle:5']); 
});