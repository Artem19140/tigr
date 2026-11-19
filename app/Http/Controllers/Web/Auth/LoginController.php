<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\Employee;
use App\Modules\Shared\ExamSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;

#[Middleware('guest:web,foreignNationals')]

class LoginController
{
    #[Post('login', 'employees.login')]
    #[Middleware('throttle:5')]
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
    
    #[Get('login', 'login')]
    public function view(): \Inertia\Response {
        return Inertia::render('Auth/Login', [
            'loginUrl' => route('employees.login', [], false),
            'codesVerificationUrl' => route('exams-codes.verification', [], false),
            'codesLength' => ExamSettings::codesLength()
        ]);
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
