<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidAttemptStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $attempt = $request->route('attempt');

        if ($attempt->isAnnulled()) {
            return $this->deny('Попытка аннулирована', $request);
        }
        if ($attempt->isFinished()) {
            return $this->deny('Попытка завершена', $request);
        }
        if ($attempt->isExpired()) {
            return $this->deny('Время попытки истекло', $request);
        }

        return $next($request);
    }

    protected function deny(
        string $message,
        Request $request
    ): JsonResponse|RedirectResponse {
        Auth::logout();
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], 401);
        }
        Inertia::flash(['error' => $message]);

        return redirect()->route('login');
    }
}
