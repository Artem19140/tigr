<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
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

        if($request->input('email') === config('app.platform_admin.email')){
            Log::warning('trying to reset admin password');
            return back()->withErrors(['status' => __('passwords.user')]);
        } 

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if($status === Password::PasswordReset){
            return back()->withErrors(['status' => __($status)]);
        }
        
        return back()->withErrors(['status' => __($status)]);
    }
}
