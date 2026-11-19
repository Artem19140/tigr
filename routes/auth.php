<?php

use App\Http\Controllers\Web\Auth\PasswordController;
use Inertia\Inertia;

Route::middleware([
    'guest:web,foreignNationals'
])->group(function () {
    Route::get('/reset-password/{token}', fn ($token) => Inertia::render('Auth/ChangePassword', [
        'token' => $token,
        'email' => request()->query('email'),
        'resetUrl' => route('password.reset.post')
    ]))->name('password.reset');

    Route::get('/forgot-password', fn () => 
        Inertia::render('Auth/ForgotPassword', [
            'loginUrl' => route('login'),
            'forgotUrl' => route('password.email')
        ])
    )->name('password.forgot');

    Route::post('/forgot-password', [PasswordController::class, 'forgot'])
        ->name('password.email');

    Route::post('password/reset', [PasswordController::class, 'change'])
        ->name('password.reset.post');
});