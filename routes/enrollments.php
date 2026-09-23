<?php

use App\Http\Controllers\Web\Enrollment\EnrollmentController;
use App\Models\Enrollment;


Route::post('/enrollments', [EnrollmentController::class, 'store'])
    ->name('enrollments.store')
    ->can('create', Enrollment::class);

Route::put('enrollments/{enrollment}/payment', [EnrollmentController::class, 'changePayment'])
    ->middleware('can:payment,enrollment')
    ->name('enrollments.payment-change');

Route::get('enrollments/{enrollment}/statements', [EnrollmentController::class, 'statement'])
    ->middleware('can:statement,enrollment')
    ->name('enrollments.statements');
