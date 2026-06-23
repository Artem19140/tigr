<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordController
{
    public function reset(
        PasswordResetRequest $request,
        Employee $employee
    ): JsonResponse {
        if ($employee->isPlatformAdmin()) {
            abort(403);
        }
        $wrongPassword = ! Hash::check(
            $request->validated('adminPassword'),
            $request->user()->password
        );

        if ($wrongPassword) {
            throw ValidationException::withMessages([
                'adminPassword' => 'Неверные учетные данные',
            ]);
        }

        $employee->password = Hash::make($request->validated('password'));
        $employee->has_to_change_password = true;

        $employee->save();
        Log::info('password_reset', [
            'employee_id' => $employee->id,
        ]);

        return response()->json();
    }

    public function change(ChangePasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function(Employee $employee, string $password){
                $employee->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $employee->password_set_at = Carbon::now();
                $employee->save();
                
                Auth::guard('web')->login($employee);
                Auth::logoutOtherDevices($password);
                
                event(new PasswordReset($employee));
            }
        );

        if($status === Password::PasswordReset){
            return redirect()->to('me');
        }
        
        throw ValidationException::withMessages([
            'email' => [__($status)]
        ]);
    }

    public function forgot(Request $request) 
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if($status === Password::PasswordReset){
            return back()->withErrors(['status' => __($status)]);
        }

        return back()->withErrors(['status' => __($status)]);
    }
}
