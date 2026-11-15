<?php

namespace App\Http\Controllers\Web\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\RouteAttributes\Attributes\Post;

#[Middleware('web')]
class LogoutController
{
    #[Post('logout', 'logout')]
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    #[Post('logout/all', 'logout.all')]
    public function logoutAll(Request $request): Response
    {
        $request->validate(['password' => ['required']]);

        $password = $request->input('password');

        $passwordWrong = ! Hash::check($password, $request->user()->password);

        if ($passwordWrong) {
            throw ValidationException::withMessages(['password' => 'Неверные учетные данные']);
        }

        Auth::logoutOtherDevices($password);
        Log::info('logout_other_divices', []);

        return response()->noContent();
    }
}
