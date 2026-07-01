<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\PasswordController;
use Inertia\Inertia;

Route::middleware([
    'meta',
    'guest:web,foreignNationals'
])->group(function () {
    Route::inertia('login', 'Auth/Login')
        ->name('login');

    Route::post('login', [LoginController::class, 'login'])
        ->middleware(['throttle:5']);

    Route::get('/reset-password/{token}', fn ($token) => Inertia::render('Auth/ChangePassword', [
        'token' => $token,
        'email' => request()->query('email')
    ]))->name('password.reset');

    Route::get('/forgot-password', fn () => 
        Inertia::render('Auth/ForgotPassword', [])
    )->name('password.forgot');

    Route::post('/forgot-password', [PasswordController::class, 'forgot'])->name('password.email');

    Route::post('password/reset', [PasswordController::class, 'change']);
});