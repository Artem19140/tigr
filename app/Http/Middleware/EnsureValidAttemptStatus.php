<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            $this->log('Попытка аннулирована', $attempt->id);
            return $this->deny('Попытка аннулирована', $request);
        }
        if ($attempt->isFinished()) {
            $this->log('Попытка завершена', $attempt->id);
            return $this->deny('Попытка завершена', $request);
        }
        if ($attempt->isExpired()) {
            $this->log('Время попытки истекло', $attempt->id);
            return $this->deny('Время попытки истекло', $request);
        }

        return $next($request);
    }

    protected function deny(
        string $message,
        Request $request
    ): JsonResponse|RedirectResponse {
        
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], 400);
        }
        Inertia::flash(['error' => $message]);
        Auth::logout();
        return redirect()->route('login');
    }

    protected function log(
        string $message,
        int $attemptId
    ){
        Log::info($message, [
            'attempt_id' => $attemptId
        ]);
    }
}
