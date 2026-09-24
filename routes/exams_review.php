<?php

use App\Http\Controllers\Web\ExamReview\AttemptReviewController;
use App\Http\Controllers\Web\ExamReview\ExamReviewController;

Route::middleware(
    'can:examiner,exam'
)
    ->prefix('exams')
    ->group(function () {

    Route::get('{exam}/review', [ExamReviewController::class, 'show'])
        ->name('exams.review');

    
    
});

Route::middleware(
    'can:attempts.employee-access,attempt'
)
->prefix('attempts')
->group(function () {

    Route::put('{attempt}/answers/{attempt_answer}/rate', [AttemptReviewController::class, 'rateAnswer'])
        ->name('attempts.answers.rate')
        ->scopeBindings();

    Route::get('{attempt}/review', [AttemptReviewController::class, 'show'])
        ->name('attempts.review');
        
    Route::post('{attempt}/review/finish', [AttemptReviewController::class, 'finish'])
        ->name('attempts.review.finish');
    
});