<?php

use App\Http\Controllers\Web\ExamSession\AnswerController;
use App\Http\Controllers\Web\ExamSession\AttemptTakingController;
use App\Http\Controllers\Web\ExamSession\VerifyCodeController;
use App\Support\AppMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
        'meta',
        'guest:web,foreignNationals'
    ])->group(function () {
        
        Route::inertia('attempts/finish', 'Attempt/AfterAttempt')
            ->name('attempts.finish.after');

        Route::post('exam-codes/verify', [VerifyCodeController::class, 'verify'])
            ->middleware(['throttle:10']); 
    });
    
Route::prefix('attempts')
    ->middleware([
        'meta',
        'auth:foreignNationals',
        'can:attempts.foreign-national-access,attempt',
        AppMiddleware::ENSURE_ATTEMPT_VALID_STATUS,
    ])
    ->group(function () {
        Route::put('{attempt}/finish', [AttemptTakingController::class, 'finish'])
            ->name('attempts.finish');

        Route::get('{attempt}', [AttemptTakingController::class, 'show'])
            ->name('attempts.show');

        Route::put('{attempt}', [AttemptTakingController::class, 'start'])
            ->name('attempts.start');

        Route::put('{attempt}/answers/{attemptAnswer}', [AnswerController::class, 'update'])
            ->scopeBindings()
            ->name('attempts.answers.update');

        Route::put('{attempt}/answers/{attemptAnswer}/audio', [AttemptTakingController::class, 'audioPlayed'])
            ->scopeBindings()
            ->name('attempts.answers.update.audio');
    });



    