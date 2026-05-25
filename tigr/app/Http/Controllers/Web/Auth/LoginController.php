<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController
{
    public function login(LoginRequest $request): RedirectResponse
    {

        if ($this->wrongCredentials($request)) {
            throw ValidationException::withMessages([
                'email' => 'Неверные учетные данные.',
                'password' => '',
            ]);
        }

        $employee = Auth::user();

        $employee->loadMissing('center');
        if (! $employee->isActive() || ! $employee->center->isActive()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Неверные учетные данные.',
                'password' => '',
            ]);
        }

        $request->session()->regenerate();

        if ($employee->hasChangePassword()) {
            return redirect()->route('password.change');
        }

        return redirect()->to($employee->resolveRedirect());
    }

    protected function wrongCredentials(LoginRequest $request): bool
    {
        $wrongCredentials = ! Auth::attempt([
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ], $request->validated('rememberMe'));

        return $wrongCredentials;
    }
}
