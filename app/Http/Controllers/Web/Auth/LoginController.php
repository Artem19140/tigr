<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController
{
    public function login(LoginRequest $request): RedirectResponse
    {
        $this->verifyCredentials(
            $request->validated('email'),
            $request->validated('password'),
            $request->validated('rememberMe')
        );

        $employee = Auth::user();
        
        $this->ensureHasAccess($employee);

        $request->session()->regenerate();

        return redirect()->to($employee->resolveRedirect());
    }

    protected function verifyCredentials(
        string $email,
        string $password,
        bool $rememberMe
    ): void
    {
        $wrongCredentials = ! Auth::attempt([
            'email' => $email,
            'password' => $password,
        ], $rememberMe);

        if ($wrongCredentials) {
            throw ValidationException::withMessages([
                'email' => 'Неверные учетные данные.',
                'password' => '',
            ]);
        }
    }

    protected function ensureHasAccess(Employee $employee):void{
        if ($this->hasNoAccess($employee)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Неверные учетные данные.',
                'password' => '',
            ]);
        }
    }

    protected function hasNoAccess(Employee $employee):bool
    {
        if($employee->isPlatformAdmin()){
            return false;
        }
        return ! $employee->isActive();
    }
}
