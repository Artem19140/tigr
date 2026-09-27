<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
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

Route::middleware([
    'meta',
    'auth'
])->group(function(){
    Route::post('logout', [LogoutController::class, 'logout'])
        ->name('logout');
        
    Route::post('logout/all', [LogoutController::class, 'logoutAll'])
        ->name('logout.all');
});