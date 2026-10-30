<?php

use App\Http\Controllers\Web\ExamSession\AnswerController;
use App\Http\Controllers\Web\ExamSession\AttemptTakingController;
use App\Http\Controllers\Web\ExamSession\ExamSessionController;
use App\Http\Controllers\Web\ExamSession\PersonConfirmationController;
use App\Http\Controllers\Web\ExamSession\VerifyCodeController;
use App\Support\AppMiddleware;
use Inertia\Inertia;

Route::middleware([
        'meta',
        'guest:web,foreignNationals'
    ])->group(function () {
        
        Route::get('attempts/finish', function(){
            return Inertia::render('ExamSession/AfterTest', [
                'redirectUrl' => route('login', [], false)
            ]);
        })->name('attempts.finish.after');

        Route::post('exams-codes/verification', [VerifyCodeController::class, 'verify'])
            ->name('exams-codes.verification')
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
        Route::post('{attempt}/finish', [AttemptTakingController::class, 'finish'])
            ->name('attempts.finish');

        Route::get('{attempt}', [AttemptTakingController::class, 'show'])
            ->name('attempts.show');

        Route::put('{attempt}/answers/{attempt_answer}', [AnswerController::class, 'update'])
            ->scopeBindings()
            ->name('attempts.answers.update');

        Route::put(
            '{attempt}/answers/{attempt_answer}/audio', 
            [AttemptTakingController::class, 'audioPlayed']
        )
            ->scopeBindings()
            ->name('attempts.answers.audio.update');

    });

Route::prefix('exams-sessions')
    ->middleware([
        'meta'
    ])
    ->group(function () {
        Route::get(
            'confirmation', 
            [PersonConfirmationController::class, 'confirmationView']
        )
            ->name('exams-sessions.confirmation.show');

        Route::post(
            'confirmation', 
            [PersonConfirmationController::class, 'confirmation']
        )
            ->name('exams-sessions.confirmation');
        
        Route::post(
            'confirmation/rejection', 
            [PersonConfirmationController::class, 'rejection']
        )
            ->name('exams-sessions.confirmation-rejection');
        
    });

Route::prefix('exams-sessions/enrollments/{enrollment}')
    ->middleware([
        'auth:foreignNationals',
        'can:examSession,enrollment'
    ])

    ->group( function() {
        Route::get(
            'info', 
            [ExamSessionController::class, 'info']
        )->name('exams-sessions.info');

         Route::get(
            'sound-setting', 
            [ExamSessionController::class, 'soundSetting']
        )->name('exams-sessions.sound-setting');

        Route::post(
            'attempts', 
            [ExamSessionController::class, 'start']
        )->name('exams-sessions.start');
}); 